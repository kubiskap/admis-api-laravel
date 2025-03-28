<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Models\Views\ExternalApi;

/**
 * @OA\Tag(
 *     name="External API",
 *     description="Operations for External API view"
 * )
 */
class ExternalApiViewController extends APIBaseController
{
    /**
     * @OA\Get(
     *     path="/api/v1/external",
     *     summary="List external API data",
     *     description="Returns records from the External API view with optional date filtering.",
     *     tags={"External API"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         description="Filter records from this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         description="Filter records until this date (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="nazev_projektu", type="string", example="II/666 Netvořice, rekonstrukce,"),
     *                 @OA\Property(property="id_projektu", type="integer", example=2),
     *                 @OA\Property(property="charakter_projektu", type="string", example="Projekt stavby"),
     *                 @OA\Property(property="faze_projektu", type="string", example="Zrealizováno"),
     *                 @OA\Property(property="cena", type="integer", example=60000),
     *                 @OA\Property(property="geo_body", type="string", nullable=true, example="MULTILINESTRING((14.000 49.000, 14.001 49.001),(14.000 49.000, 14.001 49.001), ...)"),
     *                 @OA\Property(
     *                      property="komunikace_array",
     *                      type="array",
     *                      @OA\Items(
     *                          @OA\Property(property="komunikace_nazev", type="string", example="666"),
     *                          @OA\Property(property="staniceni_od", type="float", example="0.000"),
     *                          @OA\Property(property="staniceni_do", type="float", example="1.234"),
     *                          @OA\Property(property="staniceni_od_uls", type="float", example="0.000"),
     *                          @OA\Property(property="staniceni_do_uls", type="float", example="1.234"),
     *                          @OA\Property(property="uls_verze", type="string", example="202010"),
     *                      )
     *                 ),
     *                 @OA\Property(property="odkaz", type="string", example="http://admistrace/projekt/2"),
     *                 @OA\Property(property="datum_posledni_zmeny", type="string", format="date-time", example="2020-10-29T09:53:03.000000Z"),
     *                 @OA\Property(property="popis_projektu", type="string", example="Popis projektu..."),
     *                 @OA\Property(property="editor_jmeno", type="string", example="Jan Novák"),
     *                 @OA\Property(property="editor_username", type="string", example="jan.novak"),
     *                 @OA\Property(property="priorita_skore", type="integer", nullable=true),
     *                 @OA\Property(property="priorita_korekce", type="integer", nullable=true),
     *                 @OA\Property(property="predani_staveniste", type="string", format="date-time"),
     *                 @OA\Property(property="dokonceni_stavby", type="string", format="date-time"),
     *                 @OA\Property(property="zaruka_technologicka", type="string", format="date-time"),
     *                 @OA\Property(property="zaruka_stavebni", type="string", format="date-time"),
     *                 @OA\Property(property="zdroj_financovani_pd", type="string", nullable=true),
     *                 @OA\Property(property="zdroj_financovani_stavby", type="string", example="IROP"),
     *                 @OA\Property(property="okres", type="string", example="Benešov")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *        response=401,
     *        description="Unauthenticated"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 15);
        $query = ExternalApi::query();

        $dateFrom = $request->input('date_from');
       // $dateTo   = $request->input('date_to');

        if ($dateFrom) {
            $query->where('datum_posledni_zmeny', '>=', $dateFrom);
        }

       /* if ($dateTo) {
            $query->where('datum_posledni_zmeny', '<=', $dateTo);
        }

        if ($dateFrom && $dateTo) {
            $query->whereBetween('datum_posledni_zmeny', [
                "{$dateFrom} 00:00:00",
                "{$dateTo} 23:59:59"
            ]);
        }*/

        return response()->json($query->get());
    }
}
