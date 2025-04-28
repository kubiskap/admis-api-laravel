<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ActionLogResource",
 *     type="object",
 *     title="Action Log Resource",
 *     required={"id", "date", "user", "action"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the action log"
 *     ),
 *     @OA\Property(
 *         property="date",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the action was logged"
 *     ),
 *     @OA\Property(
 *         property="user",
 *         type="object",
 *         description="User details",
 *         required={"username", "name"},
 *         @OA\Property(
 *             property="username",
 *             type="string",
 *             description="Username of the user"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Full name of the user"
 *         )
 *     ),
 *     @OA\Property(
 *         property="action",
 *         type="string",
 *         description="Name of the action performed"
 *     ),
 *     @OA\Property(
 *         property="project",
 *         type="object",
 *         description="Project details, if available",
 *         nullable=true,
 *         @OA\Property(
 *             property="id",
 *             type="integer",
 *             description="Project unique identifier"
 *         ),
 *         @OA\Property(
 *             property="name",
 *             type="string",
 *             description="Project name"
 *         ),
 *         @OA\Property(
 *             property="phase",
 *             type="string",
 *             description="Name of the project phase"
 *         )
 *     )
 * )
 */
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
