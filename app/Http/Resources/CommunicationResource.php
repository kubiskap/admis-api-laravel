<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            $this->mergeWhen(!$isSearch, [
                'gps_n1' => $this->pivot->gpsN1,
                'gps_n2' => $this->pivot->gpsN2,
                'gps_e1' => $this->pivot->gpsE1,
                'gps_e2' => $this->pivot->gpsE2,
                'all_points' => $this->pivot->allPoints,
                'geometry' => $this->pivot->geometry,
            ]),
        ];
    }
}
