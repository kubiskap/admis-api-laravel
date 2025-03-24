<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project\Project;
use App\Models\Pivots\ProjectCommunication;
use App\Models\Enums\Communication;

class ExternalApiController extends Controller
{

    public function index(Request $request)
    {

        $perPage = $request->query('per_page', 15);
        $page = $request->query('page', 1);
        
        $projects = Project::whereNull('deletedDate')
            ->with([
                'projectType',
                'projectSubtype',
                'financialSource',
                'financialSourcePd',
                'phase',
                'editorUser',
                'authorUser',
                'areas',
                'communications',
                'companies',
                'contacts',
                'prices',
                'versions',
                'deadlines',
                'priorityScore'
            ])
            ->paginate($perPage);

        $rows = [];
        foreach ($projects as $project) {
            if ($project->communications->isEmpty()) {
                // no communications => produce 1 row with pivot-based columns null
                $rows[] = $this->transformRow($project, null, null);
            } else {
                // multiple communications => produce multiple rows
                foreach ($project->communications as $comm) {
                    $rows[] = $this->transformRow($project, $comm->pivot, $comm);
                }
            }
        }

        return response()->json($rows);
    }

    /**
     * Flatten one Project + (optionally) one communication pivot
     * into a single array, replicating your old view columns.
     */
    private function transformRow(
        Project $project,
        ?ProjectCommunication $pivot,
        ?Communication $comm
    ): array {
        // --- 1) "cena" (take first price if multiple)
        $priceRow = $project->prices->first();
        $cena = $priceRow ? $priceRow->value : null;

        // --- 2) "datum_posledni_zmeny" => max of versions.created
        $lastVersionDate = $project->versions->max('created');

        // --- 3) "priorita_skore" & "priorita_korekce"
        $prioritaSkore   = optional($project->priorityScore)->priorityScore;
        $prioritaKorekce = optional($project->priorityScore)->correctionValue;

        // --- 4) deadlines: predani_staveniste(12), dokonceni_stavby(24),
        //                  zaruka_technologicka(25), zaruka_stavebni(26)
        $predani = $project->deadlines->firstWhere('idDeadlineType', 12);
        $predaniStaveniste = $predani ? $predani->value : null;

        $dokonceni = $project->deadlines->firstWhere('idDeadlineType', 24);
        $dokonceniStavby = $dokonceni ? $dokonceni->value : null;

        $zarukaTech = $project->deadlines->firstWhere('idDeadlineType', 25);
        $zarukaTechnologicka = $zarukaTech ? $zarukaTech->value : null;

        $zarukaStav = $project->deadlines->firstWhere('idDeadlineType', 26);
        $zarukaStavebni = $zarukaStav ? $zarukaStav->value : null;

        // --- 5) "zdroj_financovani_pd" & "zdroj_financovani_stavby"
        $zdrojFinStavby = optional($project->financialSource)->name;
        $zdrojFinPd     = optional($project->financialSourcePD)->name;

        // Build geometry
        $geoBody = null;
        if ($pivot) {
            $geomVal = $pivot->geometry ?? $pivot->allPoints;
            if ($geomVal) {
                $geoBody = "MULTILINESTRING($geomVal)";
            }
        }

        $areaNames = $project->areas->pluck('name')->all();

        return [
            'nazev_projektu'       => $project->name,
            'id_projektu'          => $project->idProject,
            'charakter_projektu'   => optional($project->projectType)->name,
            'faze_projektu'        => optional($project->phase)->name,
            'cena'                 => $cena,

            'gps_n1'      => $pivot ? $pivot->gpsN1 : null,
            'gps_n2'      => $pivot ? $pivot->gpsN2 : null,
            'gps_e1'      => $pivot ? $pivot->gpsE1 : null,
            'gps_e2'      => $pivot ? $pivot->gpsE2 : null,
            'geo_body'    => $geoBody,
            'staniceni_od'=> $pivot ? $pivot->stationingFrom : null,
            'staniceni_do'=> $pivot ? $pivot->stationingTo : null,
            'cislo_komunikace' => $comm ? $comm->name : null,

            'odkaz' => 'http://admistrace/projekt/' . $project->idProject,

            'datum_posledni_zmeny'  => $lastVersionDate,
            'popis_projektu'        => $project->subject,
            'editor_jmeno'          => optional($project->editorUser)->name,
            'editor_username'       => $project->editor,

            'priorita_skore'        => $prioritaSkore,
            'priorita_korekce'      => $prioritaKorekce,

            'predani_staveniste'    => $predaniStaveniste,
            'dokonceni_stavby'      => $dokonceniStavby,
            'zaruka_technologicka'  => $zarukaTechnologicka,
            'zaruka_stavebni'       => $zarukaStavebni,

            'zdroj_financovani_pd'      => $zdrojFinPd,
            'zdroj_financovani_stavby'  => $zdrojFinStavby,

            // Example for area (if you want them as a single field)
            'okres' => implode(', ', $areaNames),
        ];
    }
}
