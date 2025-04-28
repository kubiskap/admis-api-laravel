<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CommunicationResource",
 *     type="object",
 *     title="Communication Resource",
 *     required={"name", "stationing_from", "stationing_to"},
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Communication name"
 *     ),
 *     @OA\Property(
 *         property="stationing_from",
 *         type="number",
 *         description="Stationing from value"
 *     ),
 *     @OA\Property(
 *         property="stationing_to",
 *         type="number",
 *         description="Stationing to value"
 *     ),
 *     @OA\Property(
 *         property="geometry",
 *         type="string",
 *         description="Geometry information",
 *         nullable=true
 *     )
 * )
 */
class CommunicationResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Is this a project search request?
        $isSearch = $request->routeIs('projects.search');

        return [
            'name' => $this->name,
            'stationing_from' => $this->pivot->stationingFrom,
            'stationing_to' => $this->pivot->stationingTo,
            $this->mergeWhen(!$isSearch, new CommunicationGeometryResource($this->pivot)),
        ];
    }
}
