<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="ProjectResource",
 *     type="object",
 *     title="Project Resource",
 *     required={"id", "name", "type", "in_concept"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier of the project"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Project name"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Project type name"
 *     ),
 *     @OA\Property(
 *         property="subtype",
 *         type="string",
 *         description="Project subtype name",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="in_concept",
 *         type="boolean",
 *         description="Indicates if the project is in concept"
 *     ),
 *     @OA\Property(
 *         property="phase",
 *         type="object",
 *         description="Project phase details",
 *         nullable=true,
 *         @OA\Property(property="id", type="integer", description="Phase identifier"),
 *         @OA\Property(property="name", type="string", description="Phase name"),
 *         @OA\Property(property="color", type="string", description="Phase color"),
 *         @OA\Property(property="color_class", type="string", description="Phase color class")
 *     ),
 *     @OA\Property(
 *         property="editor",
 *         type="object",
 *         description="Editor details",
 *         nullable=true,
 *         @OA\Property(property="username", type="string", description="Editor username"),
 *         @OA\Property(property="name", type="string", description="Editor name")
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="object",
 *         description="Author details",
 *         nullable=true,
 *         @OA\Property(property="username", type="string", description="Author username"),
 *         @OA\Property(property="name", type="string", description="Author name")
 *     ),
 *     @OA\Property(
 *         property="priority_attributes",
 *         type="object",
 *         description="Priority attributes for the project"
 *     ),
 *     @OA\Property(
 *         property="financial_source",
 *         type="string",
 *         description="Name of the financial source",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="financial_source_pd",
 *         type="string",
 *         description="Name of the project documentation financial source",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="areas",
 *         type="array",
 *         description="List of area names",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(
 *         property="communications",
 *         type="array",
 *         description="List of communications",
 *         @OA\Items(ref="#/components/schemas/CommunicationResource")
 *     ),
 *     @OA\Property(
 *         property="contacts",
 *         type="array",
 *         description="List of contacts",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", description="Contact identifier"),
 *             @OA\Property(property="name", type="string", description="Contact name"),
 *             @OA\Property(property="phone", type="string", description="Contact phone"),
 *             @OA\Property(property="email", type="string", description="Contact email"),
 *             @OA\Property(
 *                 property="type",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", description="Contact type identifier"),
 *                 @OA\Property(property="name", type="string", description="Contact type name")
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="companies",
 *         type="array",
 *         description="List of companies",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", description="Company identifier"),
 *             @OA\Property(property="name", type="string", description="Company name"),
 *             @OA\Property(property="address", type="string", description="Company address"),
 *             @OA\Property(property="ic", type="string", description="Company IC"),
 *             @OA\Property(property="dic", type="string", description="Company DIC"),
 *             @OA\Property(property="www", type="string", description="Company website"),
 *             @OA\Property(
 *                 property="type",
 *                 type="object",
 *                 @OA\Property(property="id", type="string", description="Company type identifier"),
 *                 @OA\Property(property="name", type="string", description="Company type name")
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="prices",
 *         type="array",
 *         description="List of prices",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(
 *                 property="type",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", description="Price type identifier"),
 *                 @OA\Property(property="name", type="string", description="Price type name"),
 *                 @OA\Property(
 *                     property="subtype",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", description="Price subtype identifier"),
 *                     @OA\Property(property="name", type="string", description="Price subtype name")
 *                 )
 *             ),
 *             @OA\Property(property="value", type="number", description="Price value")
 *         )
 *     ),
 *     @OA\Property(
 *         property="deadlines",
 *         type="array",
 *         description="List of deadlines",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="type", type="string", description="Deadline type"),
 *             @OA\Property(property="date", type="string", format="date", description="Deadline date")
 *         )
 *     ),
 *     @OA\Property(
 *         property="suspensions",
 *         type="array",
 *         description="List of suspensions",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="source", type="string", description="Suspension source"),
 *             @OA\Property(property="reason", type="string", description="Suspension reason"),
 *             @OA\Property(property="comment", type="string", description="Suspension comment"),
 *             @OA\Property(property="start", type="string", format="date-time", description="Suspension start date"),
 *             @OA\Property(property="end", type="string", format="date-time", description="Suspension end date"),
 *             @OA\Property(property="author", type="string", description="Author of the suspension")
 *         )
 *     ),
 *     @OA\Property(
 *         property="tasks",
 *         type="array",
 *         description="List of tasks",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="author", type="string", description="Task author"),
 *             @OA\Property(property="date", type="string", format="date-time", description="Task creation date"),
 *             @OA\Property(property="deadline", type="string", format="date-time", description="Task deadline"),
 *             @OA\Property(property="name", type="string", description="Task name"),
 *             @OA\Property(property="description", type="string", description="Task description"),
 *             @OA\Property(
 *                 property="status",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", description="Task status identifier"),
 *                 @OA\Property(property="name", type="string", description="Task status name"),
 *                 @OA\Property(property="color", type="string", description="Task status color"),
 *                 @OA\Property(property="color_class", type="string", description="Task status color class")
 *             ),
 *             @OA\Property(
 *                 property="reactions",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="author", type="string", description="Reaction author"),
 *                     @OA\Property(property="date", type="string", format="date-time", description="Reaction date"),
 *                     @OA\Property(property="content", type="string", description="Reaction content")
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class ProjectResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->idProject,
            'name' => $this->name,
            'type' => $this->projectType->name,
            'subtype' => $this->projectSubtype?->name,
            'in_concept' => $this->inConcept,
            'phase' => $this->whenLoaded('phase', function() {
                return [
                    'id' => $this->phase->idPhase,
                    'name' => $this->phase->name,
                    'color' => $this->phase->phaseColor,
                    'color_class' => $this->phase->phaseColorClass,
                ];
            }),
            'editor' => $this->whenLoaded('editorUser', function() {
                return [
                    'username' => $this->editorUser->username,
                    'name' => $this->editorUser->name,
                ];
            }),
            'author' => $this->whenLoaded('authorUser', function() {
                return [
                    'username' => $this->authorUser->username,
                    'name' => $this->authorUser->name,
                ];
            }),
            'priority_attributes' => $this->priorityAtts,
            'financial_source' => $this->whenLoaded('financialSource', function() {
                return $this->financialSource->name;
            }),
            'financial_source_pd' => $this->whenLoaded('financialSourcePD', function() {
                return $this->financialSourcePD->name;
            }),
            'areas' => $this->whenLoaded('areas', function() {
                return $this->areas->pluck('name');
            }),
            'communications' => $this->whenLoaded('communications', CommunicationResource::collection($this->communications)),
            'contacts' => $this->whenLoaded('contacts', function() {
                return $this->contacts->map(function($contact) {
                    return [
                        'id' => $contact->idContact,
                        'name' => $contact->name,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                        'type' => [
                            'id' => $contact->pivot->contactType->idContactType,
                            'name' => $contact->pivot->contactType->name,
                        ],
                    ];
                });
            }),
            'companies' => $this->whenLoaded('companies', function() {
                return $this->companies->map(function($company) {
                    return [
                        'id' => $company->idCompany,
                        'name' => $company->name,
                        'address' => $company->address,
                        'ic' => $company->ic,
                        'dic' => $company->dic,
                        'www' => $company->www,
                        'type' => [
                            'id' => $company->pivot->companyType->name,
                            'name' => $company->pivot->companyType->name,
                        ],
                    ];
                });
            }),
            'prices' => $this->whenLoaded('prices', function() {
                return $this->prices->map(function($price) {
                    return [
                        'type' => [
                            'id' => $price->priceType->idPriceType,
                            'name' => $price->priceType->name,
                            'subtype' => [
                                'id' => $price->priceType->priceSubtype->idPriceSubtypes,
                                'name' => $price->priceType->priceSubtype->name,
                            ],
                        ],
                        'value' => $price->value,
                    ];
                });
            }),
            'deadlines' => $this->whenLoaded('deadlines', function() {
                return $this->deadlines->map(function($deadline) {
                    return [
                        'type' => $deadline->deadlineType->name,
                        'date' => $deadline->value,
                    ];
                });
            }),
            'suspensions' => $this->whenLoaded('suspensions', function() {
                return $this->suspensions->map(function($suspension) {
                    return [
                        'source' => $suspension->suspensionSource->name,
                        'reason' => $suspension->suspensionReason->name,
                        'comment' => $suspension->comment,
                        'start' => $suspension->dateFrom,
                        'end' => $suspension->dateTo,
                        'author' => $suspension->username
                    ];
                });
            }),
            'tasks' => $this->whenLoaded('tasks', function () {
                return $this->tasks->where('deleted', null)->map(function($task) {
                    return [
                        'author' => $task->createdBy,
                        'date' => $task->created,
                        'deadline' => $task->latestVersion->deadlineTo,
                        'name' => $task->latestVersion->name,
                        'description' => $task->latestVersion->description,
                        'status' => [
                            'id' => $task->latestVersion->status->idTaskStatus,
                            'name' => $task->latestVersion->status->name,
                            'color' => $task->latestVersion->status->statusColor,
                            'color_class' => $task->latestVersion->status->statusClass,
                        ],
                        'reactions' => $task->reactions?->map(function($reaction) {
                            return [
                                'author' => $reaction->createdBy,
                                'date' => $reaction->created,
                                'content' => $reaction->content,
                            ];
                        }),
                    ];
                });
            })
        ];
    }
}
