<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany
};

class Project extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'projects';

    /**
     * The primary key on this table.
     */
    protected $primaryKey = 'idProject';

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Many projects belong to one ProjectType (rangeProjectTypes).
     * projects.idProjectType -> rangeProjectTypes.idProjectType
     */
    public function projectType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ProjectType::class, 'idProjectType', 'idProjectType');
    }

    /**
     * Many projects belong to one ProjectSubtype (rangeProjectSubtypes).
     * projects.idProjectSubtype -> rangeProjectSubtypes.idProjectSubtype
     */
    public function projectSubtype(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ProjectSubtype::class, 'idProjectSubtype', 'idProjectSubtype');
    }

    /**
     * Many projects belong to one FinancialSource (rangeFinancialSources).
     * projects.idFinSource -> rangeFinancialSources.idFinSource
     */
    public function financialSource(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\FinancialSource::class, 'idFinSource', 'idFinSource');
    }

    /**
     * Many projects belong to one Phase (rangePhases).
     * projects.idPhase -> rangePhases.idPhase
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Phase::class, 'idPhase', 'idPhase');
    }

    /**
     * Link projects to 'areas' through project2area (M:N pivot).
     * project2area(idProject, idArea) → rangeAreas(idArea)
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\Area::class,
            'project2area',
            'idProject',   // FK on project2area pointing to this table
            'idArea'       // FK on project2area pointing to rangeAreas
        );
    }

    /**
     * Link projects to 'companies' through project2company (M:N pivot).
     * project2company(idProject, idCompany) → rangeCompanies(idCompany)
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\Company::class,
            'project2company',
            'idProject',
            'idCompany'
        )->using(\App\Models\Pivots\ProjectCompany::class)
        ->withPivot('idCompanyType');
    }

    /**
     * Link projects to 'communications' through project2communication (M:N pivot).
     * project2communication(idProject, idCommunication) → rangeCommunications(idCommunication)
     */
    public function communications(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\Communication::class,
            'project2communication',
            'idProject',
            'idCommunication'
        )
        ->withPivot([
            'stationingFrom',
            'stationingTo',
            'gpsN1',
            'gpsN2',
            'gpsE1',
            'gpsE2',
            'allPoints',
            'comment'
        ]);
    }

    /**
     * Link projects to 'contacts' through project2contact (M:N pivot).
     * project2contact(idProject, idContact) → rangeContacts(idContact)
     */
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\Contact::class,
            'project2contact',
            'idProject',
            'idContact'
        )->using(\App\Models\Pivots\ProjectContact::class)
        ->withPivot('idContactType');
    }

    /**
     * If you have an actual 'User' model in Models\, a project can have
     * an 'editor' user and an 'author' user. Note we do not
     * define belongsToMany because it's 1:1 for each column.
     */
    public function editorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'editor', 'username');
    }

    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'author', 'username');
    }
}
