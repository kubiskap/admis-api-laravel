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
            'subtype' => $this->projectSubtype->name,
            'phase' => $this->whenLoaded('phase', function() {
                return [
                    'id' => $this->phase->idPhase,
                    'name' => $this->phase->name,
                    'color' => $this->phase->phaseColor,
                    'color_class' => $this->phase->phaseColorClass,
                ];
            }),
            'editor' => $this->editorUser->username,
            'author' => $this->authorUser->username,
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
            'communications' => $this->whenLoaded('communications', function() {
                return $this->communications->map(function($comm) {
                    return [
                        'name' => $comm->name,
                        'stationing_from' => $comm->pivot->stationingFrom,
                        'stationing_to' => $comm->pivot->stationingTo,
                        'gps_n1' => $comm->pivot->gpsN1,
                        'gps_n2' => $comm->pivot->gpsN2,
                        'gps_e1' => $comm->pivot->gpsE1,
                        'gps_e2' => $comm->pivot->gpsE2,
                        'all_points' => $comm->pivot->allPoints,
                        'geometry' => $comm->pivot->geometry,
                    ];
                });
            }),
            'contacts' => $this->whenLoaded('contacts', function() {
                return $this->contacts->map(function($contact) {
                    return [
                        'name' => $contact->name,
                        'phone' => $contact->phone,
                        'email' => $contact->email,
                        'type' => $contact->pivot->contactType->name,
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
                        'type' => $company->pivot->companyType->name,
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
        ];
    }
}
