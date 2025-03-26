<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActionLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->idAction,
            'date' => $this->created,
            'user' => [
                'username' => $this->username,
                'name' => $this->user->name,
            ],
            'action' => $this->actionType->name,
            'project' => $this->whenLoaded('project', function() {
                return [
                    'id' => $this->project->idProject,
                    'name' => $this->project->name,
                    'phase' => $this->project->phase->name,
                ];
            }),
        ];
    }
}
