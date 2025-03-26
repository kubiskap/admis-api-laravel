<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
                            'name' =>$contact->pivot->contactType->name,
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
