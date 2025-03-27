<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Views\ExternalApi;

/**
 * @OA\Tag(
 *     name="External API",
 *     description="Operations for External API view"
 */
class ExternalApiViewController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/external",
     *     summary="List external API data",
     *     description="Returns records from the External API view with optional date filtering.",
     *     tags={"External API"},
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
     *         description="successful operation"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = ExternalApi::query();

        $dateFrom = $request->input('date_from');
        $dateTo   = $request->input('date_to');

        if ($dateFrom) {
            $query->where('datum_posledni_zmeny', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->where('datum_posledni_zmeny', '<=', $dateTo);
        }

        return response()->json($query->get());
    }
}