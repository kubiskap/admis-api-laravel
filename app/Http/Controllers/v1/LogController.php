<?php

namespace App\Http\Controllers\v1;

use Illuminate\Http\Request;
use App\Http\Resources\ActionLogResource;
use App\Models\Logs\ActionLog;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedSort;

class LogController extends APIBaseController
{

    /**
     * Get all system action logs
     *
     * @OA\Get(
     *     path="/api/v1/logs",
     *     summary="Get all system action logs",
     *     tags={"Logs"},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of logs per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort results. Usage: `sort=field` for ascending, `sort=-field` for descending. Allowed fields: `date`, `user.username`, `action`, `project.name`. Default: `-date`",
     *         required=false,
     *         @OA\Schema(type="string", default="-date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of all action logs",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/ActionLogResource")),
     *             @OA\Property(property="links", type="object",
     *                 @OA\Property(property="first", type="string", format="url", nullable=true),
     *                 @OA\Property(property="last", type="string", format="url", nullable=true),
     *                 @OA\Property(property="prev", type="string", format="url", nullable=true),
     *                 @OA\Property(property="next", type="string", format="url", nullable=true)
     *             ),
     *             @OA\Property(property="meta", type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="from", type="integer", nullable=true),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="links", type="array", @OA\Items(type="object",
     *                      @OA\Property(property="url", type="string", format="url", nullable=true),
     *                      @OA\Property(property="label", type="string"),
     *                      @OA\Property(property="active", type="boolean")
     *                 )),
     *                 @OA\Property(property="path", type="string", format="url"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="to", type="integer", nullable=true),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     ),
     *     security={{"bearerAuth":{}}}
     * )
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = ActionLog::query()->with([
            'user',         // Pro ActionLogResource
            'actionType',   // Pro ActionLogResource
            'project.phase' // Pro ActionLogResource a řazení podle project.name
        ]);

        $perPage = (int) $request->query('per_page', 15);

        $logs = QueryBuilder::for($query)
            ->allowedSorts([
                AllowedSort::field('date', 'created'),
                AllowedSort::field('user.username', 'username'), // Předpokládá, že ActionLog má sloupec 'username' nebo relaci user s 'username'
                AllowedSort::field('action', 'actionType.name'),
                AllowedSort::field('project.name', 'project.name')
            ])
            ->defaultSort('-created') // Výchozí řazení podle sloupce 'created' sestupně
            ->paginate($perPage);

        return ActionLogResource::collection($logs);
    }
}
