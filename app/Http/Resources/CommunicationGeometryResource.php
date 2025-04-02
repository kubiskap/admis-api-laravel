<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
