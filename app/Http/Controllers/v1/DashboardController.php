<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Project\Project;
use App\Models\Calendar\Deadline;
use App\Models\Calendar\CalendarEvent;

class DashboardController extends APIBaseController
{

    /**
     * 
     * GET /api/v1/dashboard/statistics
     * scope=my|all|ou, ou_id=<idOu>
     * 
     * Statistics for the dashboard:
     * - četnost staveb na osobu
     * - počet přidaných staveb v za poslední rok dle měsíců (přidané projekty)
     * - počet realizovaných staveb dle roků (ukončené projekty)
     * - počet staveb dle fáze
     * - celková cena staveb - mosty
     * - celková cena projektů
     * - počet novostaveb
     * - počet projektů
     */
    public function getStatistics(Request $request)
    {
        // Query projects by scope parameter
        $projectsQuery = $this->queryScopedProjects($request);
        $projectIds = (clone $projectsQuery)->pluck('idProject');

        // number of projects per person
        $projectsPerPerson = (clone $projectsQuery)
            ->select('editor', DB::raw('COUNT(*) as count'))
            ->groupBy('editor')
            ->with('editorUser')
            ->get()
            ->pluck('count', 'editorUser.name');


        // monthly added projects in the last year
        $addedProjects = (clone $projectsQuery)
            ->where('created', '>=', now()->subYear()->startOfMonth())
            ->select(DB::raw('DATE_FORMAT(created, "%Y-%m") as month'), DB::raw('COUNT(*) as count'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month');

        // completed projects by year
        $completedProjects = Deadline::query()
            ->where('idDeadlineType', 24)
            ->selectRaw('YEAR(value) AS year, COUNT(*) AS count')
            ->groupBy('year')
            ->orderBy('year')
            ->get()
            ->pluck('count', 'year');

        // projects by phase
        $projectsByPhase = (clone $projectsQuery)
            ->select('idPhase', DB::raw('COUNT(*) as count'))
            ->groupBy('idPhase')
            ->get()
            ->pluck('count', 'idPhase');

        // total price of bridges
        // 1) Ceny z tabulky `prices` pro projekty typu 7 (mostní)
        $bridgeProjectsPrices = \App\Models\Project\Price::query()
            ->whereIn('idProject', $projectIds)
            ->whereIn('idPriceType', [5, 6, 11])
            ->whereHas('project', fn($q) => $q->where('idProjectSubtype', 7))
            ->with('priceType')
            ->get()
            ->groupBy('idProject')
            ->map(function ($prices) {
                return $prices->sortByDesc(fn($price) => $price->priceType->ordering)->first();
            })
            ->sum(fn($price) => $price?->value ?? 0);

        // 2) Ceny z tabulky `attributes` pro ostatní projekty, kde objekt je most (objectType = 1) a atribut je cena mostu (attributeType = 3)
        $bridgeAttributesSum = \App\Models\Objects\Attribute::query()
            ->where('idAttributeType', 3)
            ->whereHas('object', function ($q) use ($projectIds) {
                $q->whereIn('idProject', $projectIds)
                ->where('idObjectType', 1);
            })
            ->sum('value');

        // Finální součet
        $sumBridgePrice = $bridgeProjectsPrices + $bridgeAttributesSum;

        // total price of projects
        $sumProjectPrice = (clone $projectsQuery)
            ->with(['prices.priceType' => function ($q) {
                $q->whereIn('idPriceType', [5, 6, 11]);
            }])
            ->get()
            ->sum(function ($project) {
                // Vybereme jen ty ceny s požadovaným priceType
                $prices = $project->prices->filter(function ($price) {
                    return in_array($price->idPriceType, [5, 6, 11]) && $price->priceType !== null;
                });

                // Najdeme cenu s nejvyšším ordering
                $best = $prices->sortByDesc(fn($p) => $p->priceType->ordering)->first();

                return $best?->value ?? 0;
            });

        // number of new construction projects
        $newConstructionCount = (clone $projectsQuery)
            ->where('idProjectType', 1)
            ->count();


        // total number of projects
        $projectCount = (clone $projectsQuery)->count();

        return response()->json([
            'projects_per_person' => $projectsPerPerson,
            'added_projects'      => $addedProjects,
            'completed_projects'  => $completedProjects,
            'projects_by_phase'   => $projectsByPhase,
            'sum_bridge_price'   => floor($sumBridgePrice),
            'sum_project_price'  => floor($sumProjectPrice),
            'new_construction_count' => $newConstructionCount,
            'project_count'       => $projectCount,
        ]);
    }

        
    /**
     * GET /api/v1/dashboard/calendar
     * from=<date>, to=<date>
     * 
     * Události pro kalendář: title, id projektu, deadline type, datum, fáze, typ události (soukromá, celá KSUS, OÚ)
     * Musíme udělat zvláštní endpoint, kde se bude definovat časový interval
     */
    public function getCalendar(Request $request)
    {
        // validate parameters
        $request->validate([
            'from' => 'required|date_format:Y-m-d',
            'to'   => 'required|date_format:Y-m-d'
        ]);

        $range = [$request->from, $request->to];

        $deadlines = Deadline::query()
            ->whereBetween('value', $range)
            ->with([
                'project.editorUser',
                'project',
                'deadlineType'
            ])
            ->get()
            ->map(function ($deadline) {
                return [
                    'type' => $deadline->deadlineType->name,
                    'date' => $deadline->value->format('Y-m-d'),
                    'project' => [
                        'id' => $deadline->project->idProject,
                        'name' => $deadline->project->name,
                        'phase' => $deadline->project->idPhase,
                        'editor' => $deadline->project->editorUser->name,
                    ],
                ];
            });

        $events = "TBD";


        return response()->json([
            'deadlines' => $deadlines,
            'events'    => $events,
        ]);
    }

    /**
     * Mé stavby
     * 
     * Neudělat spíš endpoint GET /api/v1/user/projects?
     * Bude pagination, hledání, sortování, podobně jak v enumech
     * 
     * Asi ve výsledku nedělat nic, frontend si zavolá POST /api/v1/projects/search a vyfiltruje dle uživatele
     */
    public function getMyProjects(Request $request)
    {
        
    }

    /**
     * Aktivní úkoly na mých projektech
     * 
     * Neudělat spíš endpoint GET /api/v1/user/projects/tasks?
     * Bude pagination, hledání, sortování, podobně jak v enumech
     */
    public function getMyActiveTasks(Request $request)
    {
        
    }

    /**
     * Projekty, kterým končí záruka v následujících X měsících
     * 
     * Bude zvláštní endpoint, ale kde? V projectControlleru? Tady? Třeba se bude používat i jinde.
     * Jak to udělat co nejlíp a nejuniverzálněji?
     */
    public function getExpiringWarranties($projectsQuery)
    {
        
    }

    /**
     * Poslední změny na projektech - bude zvláštní endpoint
     * 
     * Opět kde?
     */
    public function getLatestChanges(Request $request) {
        
    }

    /**
     * Creates a basic query for projects based on the scope parameter.
     * - my   : only projects, where the logged-in user is the editor
     * - all  : all projects (no restriction)
     * - ou   : projects of the editors in given ou (or the user's ou)
     */
    private function queryScopedProjects(Request $request)
    {
        // validate parameters
        $request->validate([
            'scope' => 'in:my,all,ou',
            'ou_id' => 'integer|exists:ou,idOu'
        ]);

        $scope = $request->query('scope', 'my');
        $user  = Auth::user();
        $idOu = $request->query('ou_id', $user->idOu);

        $q = Project::whereNull('deletedDate')->whereNull('deleteAuthor');

        switch ($scope) {
            case 'all':
                // no restriction
                break;

            case 'ou':
                $q->whereHas('editorUser', fn($q) => $q->where('idOu', $idOu));
                break;

            case 'my':
                $q->where('editor', $user->username);
                break;

            default:
                $q->where('editor', $user->username);
                break;
        }

        return $q;
    }

}
