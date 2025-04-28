<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CommunicationGeometryResource",
 *     type="object",
 *     title="Communication Geometry Resource",
 *     required={"gps_n1", "gps_n2", "gps_e1", "gps_e2"},
 *     @OA\Property(
 *         property="project_id",
 *         type="integer",
 *         description="Project identifier, included in map requests",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="communication_id",
 *         type="integer",
 *         description="Communication identifier, included in map requests",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="phase_color",
 *         type="string",
 *         description="Phase color from project phase, included if project is loaded",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="gps_n1",
 *         type="number",
 *         description="GPS N1 coordinate"
 *     ),
 *     @OA\Property(
 *         property="gps_n2",
 *         type="number",
 *         description="GPS N2 coordinate"
 *     ),
 *     @OA\Property(
 *         property="gps_e1",
 *         type="number",
 *         description="GPS E1 coordinate"
 *     ),
 *     @OA\Property(
 *         property="gps_e2",
 *         type="number",
 *         description="GPS E2 coordinate"
 *     ),
 *     @OA\Property(
 *         property="geometryWgs",
 *         type="string",
 *         description="Geometry in WGS coordinate system",
 *         nullable=true
 *     )
 * )
 */
class CommunicationGeometryResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Is this a map request?
        $isMap = $request->routeIs('projects.map');

        return [
            $this->mergeWhen($isMap, [
                'project_id' => $this->idProject,
                'communication_id' => $this->idCommunication,
                'phase_color' => $this->whenLoaded('project', function() {
                    return $this->project->phase->phaseColor;
                }),
            ]),
            'gps_n1' => $this->gpsN1,
            'gps_n2' => $this->gpsN2,
            'gps_e1' => $this->gpsE1,
            'gps_e2' => $this->gpsE2,
            'geometryWgs' => $this->whenNotNull($this->geometryWgs),
        ];
    }
}
