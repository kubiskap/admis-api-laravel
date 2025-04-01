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
            $this->mergeWhen(!$isSearch, new CommunicationGeometryResource($this->pivot)),
        ];
    }
}
