<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    HasManyThrough,
    HasOne,
};
use Illuminate\Support\Facades\DB;

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

    /**
     * The attributes that can be mass assigned.
     * (Add any other columns you want to allow through ->create() or ->update() calls.)
     */
    protected $fillable = [
        'idProjectType',
        'idProjectSubtype',
        'technologicalProjectType',
        'created',
        'name',
        'subject',
        'editor',
        'author',
        'idFinSource',
        'idFinSourcePD',
        'idPhase',
        'idLocalProject',
        'ginisOrAthena',
        'noteGinisOrAthena',
        'deletedDate',
        'deleteAuthor',
        'inConcept',
        'dateEvidence',
        'deadlineDurUrRequired',
        'deadlineEIARequired',
        'deadlineStudyRequired',
        'deadlineTesRequired',
        'mergedDeadlines',
        'constructionTime',
        'constructionTimeWeeksOrMonths',
        'mergePricePDAD',
        'constructionWarrantyPeriod',
        'technologyWarrantyPeriod',
        'priorityAtts',
        'passable',
    ];

    /**
     * Here, define any attribute casts (e.g., for date, boolean, JSON):
     */
    protected $casts = [
        'created' => 'datetime',
        'deletedDate' => 'datetime',
        'inConcept' => 'boolean',
        'dateEvidence' => 'boolean',
        'deadlineDurUrRequired' => 'boolean',
        'deadlineEIARequired' => 'boolean',
        'deadlineStudyRequired' => 'boolean',
        'deadlineTesRequired' => 'boolean',
        'passable' => 'boolean',
        'priorityAtts' => 'json',
    ];

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
     * Many projects belong to one Project Documentatio FinancialSource
     * projects.idFinSourcePD -> rangeFinancialSources.idFinSource
     */
    public function financialSourcePD(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\FinancialSource::class, 'idFinSourcePD', 'idFinSource');
    }

    public function priorityScore(): HasOne
    {
        return $this->hasOne(\App\Models\Views\ProjectsPriorityScore::class, 'idProject', 'idProject');
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
            'idProject',   // pivot FK referencing 'projects'
            'idArea'       // pivot FK referencing 'rangeAreas'
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
     * A project can have one or more tasks in the 'tasks' table.
     * tasks.relatedToProjectId -> projects.idProject
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\Task::class, 'relatedToProjectId', 'idProject');
    }

    /**
     * A project can have one or more price entries in the 'prices' table.
     * prices.idProject -> projects.idProject
     */
    public function prices(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Price::class, 'idProject', 'idProject');
    }

    /**
     * A project can have many deadlines.
     * deadlines.idProject -> projects.idProject
     */
    public function deadlines(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Deadline::class, 'idProject', 'idProject');
    }

    /**
     * A project can have multiple versions.
     * projectVersions.idProject -> projects.idProject
     */
    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\Project\ProjectVersion::class, 'idProject', 'idProject');
    }

    /**
     * A project can have multiple suspensions.
     * suspensions.idProject -> projects.idProject
     */
    public function suspensions(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Suspension::class, 'idProject', 'idProject');
    }

    /**
     * A project can have many actions in the log through its versions
     * actionsLogs.idLocalProject -> projectVersions.idLocalProject -> projectVersions.idProject -> projects.idProject
     */
    public function actions(): HasManyThrough
    {
        return $this->hasManyThrough(
            \App\Models\Logs\ActionLog::class,
            \App\Models\Project\ProjectVersion::class,
            'idProject',
            'idLocalProject',
            'idProject',
            'idLocalProject'
        );
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
        )->using(\App\Models\Pivots\ProjectCommunication::class)
         ->withPivot([
            'stationingFrom',
            'stationingTo',
            'gpsN1',
            'gpsN2',
            'gpsE1',
            'gpsE2',
            'allPointsWgs',
            'allPointsSjtsk',
            'geometryWgs',
            'geometrySjtsk',
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
     * A project can have an 'editor' user (projects.editor -> users.username).
     * We do not define belongsToMany because it's a 1:1 reference to one user column.
     */
    public function editorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'editor', 'username');
    }

    /**
     * A project can have an 'author' user (projects.author -> users.username).
     */
    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'author', 'username');
    }

    public function getRouteKeyName()
    {
        return 'idProject'; // use your custom key instead of 'id'
    }
}
