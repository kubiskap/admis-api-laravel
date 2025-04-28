<?php

namespace App\Models\Project;

use App\Http\Resources\ProjectResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany,
    HasMany,
    HasManyThrough,
    HasOne
};
use Illuminate\Support\Facades\DB;

/**
 * Class Project
 *
 * Represents a project in the system.
 *
 * @package App\Models\Project
 *
 * @OA\Schema(
 *     schema="Project",
 *     description="Project model",
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Unique identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="idProjectType",
 *         type="integer",
 *         description="Identifier for the project type"
 *     ),
 *     @OA\Property(
 *         property="idProjectSubtype",
 *         type="integer",
 *         description="Identifier for the project subtype"
 *     ),
 *     @OA\Property(
 *         property="technologicalProjectType",
 *         type="string",
 *         description="The technological type of the project"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the project was created"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the project"
 *     ),
 *     @OA\Property(
 *         property="subject",
 *         type="string",
 *         description="Subject of the project"
 *     ),
 *     @OA\Property(
 *         property="editor",
 *         type="string",
 *         description="Username of the project's editor"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="Username of the project's author"
 *     ),
 *     @OA\Property(
 *         property="idFinSource",
 *         type="integer",
 *         description="Identifier for the financial source"
 *     ),
 *     @OA\Property(
 *         property="idFinSourcePD",
 *         type="integer",
 *         description="Identifier for the PD financial source"
 *     ),
 *     @OA\Property(
 *         property="idPhase",
 *         type="integer",
 *         description="Identifier for the project phase"
 *     ),
 *     @OA\Property(
 *         property="idLocalProject",
 *         type="integer",
 *         description="Local project identifier that points to a specific project version (see ProjectVersion model)"
 *     ),
 *     @OA\Property(
 *         property="ginisOrAthena",
 *         type="string",
 *         description="Indicates whether the project uses Ginis or Athena"
 *     ),
 *     @OA\Property(
 *         property="noteGinisOrAthena",
 *         type="string",
 *         description="Additional note regarding Ginis or Athena"
 *     ),
 *     @OA\Property(
 *         property="deletedDate",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the project was deleted"
 *     ),
 *     @OA\Property(
 *         property="deleteAuthor",
 *         type="string",
 *         description="Username of the user who deleted the project"
 *     ),
 *     @OA\Property(
 *         property="inConcept",
 *         type="boolean",
 *         description="Indicates if the project is in the concept stage"
 *     ),
 *     @OA\Property(
 *         property="dateEvidence",
 *         type="boolean",
 *         description="Indicates if the project has date evidence"
 *     ),
 *     @OA\Property(
 *         property="deadlineDurUrRequired",
 *         type="boolean",
 *         description="Indicates if DUR/UR deadline is required"
 *     ),
 *     @OA\Property(
 *         property="deadlineEIARequired",
 *         type="boolean",
 *         description="Indicates if EIA deadline is required"
 *     ),
 *     @OA\Property(
 *         property="deadlineStudyRequired",
 *         type="boolean",
 *         description="Indicates if study deadline is required"
 *     ),
 *     @OA\Property(
 *         property="deadlineTesRequired",
 *         type="boolean",
 *         description="Indicates if TES deadline is required"
 *     ),
 *     @OA\Property(
 *         property="mergedDeadlines",
 *         type="string",
 *         description="Info about merged deadlines"
 *     ),
 *     @OA\Property(
 *         property="constructionTime",
 *         type="string",
 *         description="Construction time details"
 *     ),
 *     @OA\Property(
 *         property="constructionTimeWeeksOrMonths",
 *         type="string",
 *         description="Construction time expressed in weeks or months"
 *     ),
 *     @OA\Property(
 *         property="mergePricePDAD",
 *         type="string",
 *         description="Merged price for PDAD"
 *     ),
 *     @OA\Property(
 *         property="constructionWarrantyPeriod",
 *         type="integer",
 *         description="Construction warranty period in months"
 *     ),
 *     @OA\Property(
 *         property="technologyWarrantyPeriod",
 *         type="integer",
 *         description="Technology warranty period in months"
 *     ),
 *     @OA\Property(
 *         property="priorityAtts",
 *         type="object",
 *         description="JSON object holding priority attributes"
 *     ),
 *     @OA\Property(
 *         property="passable",
 *         type="boolean",
 *         description="Indicates whether the project is passable"
 *     )
 * )
 */
class Project extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The primary key on this table.
     *
     * @var string
     */
    protected $primaryKey = 'idProject';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     * (Add any other columns you want to allow through ->create() or ->update() calls.)
     *
     * @var array
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
     *
     * @var array
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ProjectType::class, 'idProjectType', 'idProjectType');
    }

    /**
     * Many projects belong to one ProjectSubtype (rangeProjectSubtypes).
     * projects.idProjectSubtype -> rangeProjectSubtypes.idProjectSubtype
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectSubtype(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ProjectSubtype::class, 'idProjectSubtype', 'idProjectSubtype');
    }

    /**
     * Many projects belong to one FinancialSource (rangeFinancialSources).
     * projects.idFinSource -> rangeFinancialSources.idFinSource
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function financialSource(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\FinancialSource::class, 'idFinSource', 'idFinSource');
    }

    /**
     * Many projects belong to one FinancialSource (rangeFinancialSources) for PD.
     * projects.idFinSourcePD -> rangeFinancialSources.idFinSource
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function financialSourcePD(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\FinancialSource::class, 'idFinSourcePD', 'idFinSource');
    }

    /**
     * Many projects belong to one Phase (rangePhases).
     * projects.idPhase -> rangePhases.idPhase
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Phase::class, 'idPhase', 'idPhase');
    }

    /**
     * Link projects to 'areas' through project2area (M:N pivot).
     * project2area(idProject, idArea) → rangeAreas(idArea)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\Area::class,
            'project2area',
            'idProject',   // pivot FK referencing projects
            'idArea'       // pivot FK referencing rangeAreas
        );
    }

    /**
     * Link projects to 'companies' through project2company (M:N pivot).
     * project2company(idProject, idCompany) → rangeCompanies(idCompany)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\Task::class, 'relatedToProjectId', 'idProject');
    }

    /**
     * A project can have one or more price entries in the 'prices' table.
     * prices.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Price::class, 'idProject', 'idProject');
    }

    /**
     * A project can have many deadlines.
     * deadlines.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deadlines(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Deadline::class, 'idProject', 'idProject');
    }

    /**
     * A project can have multiple versions.
     * projectVersions.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\Project\ProjectVersion::class, 'idProject', 'idProject');
    }

    /**
     * A project can have multiple suspensions.
     * suspensions.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suspensions(): HasMany
    {
        return $this->hasMany(\App\Models\Project\Suspension::class, 'idProject', 'idProject');
    }

    /**
     * A project can have many actions in the log through its versions.
     * actionsLogs.idLocalProject -> projectVersions.idLocalProject -> projectVersions.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function editorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'editor', 'username');
    }

    /**
     * A project can have an 'author' user (projects.author -> users.username).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'author', 'username');
    }

    /**
     * Link projects to related projects through projectRelations (M:N pivot).
     * projectRelations(idProject, idProjectRelation) → projects(idProjectRelation)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function relatedProjects(): BelongsToMany
    {
        return $this->belongsToMany(
            self::class,
            'projectRelations',
            'idProject',
            'idProjectRelation'
        )->using(\App\Models\Pivots\ProjectRelation::class)
         ->withPivot(['idRelationType', 'username', 'created'])
         ->withTimestamps();
    }

    /**
     * A project can have many objects.
     * objects.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function objects(): HasMany
    {
        return $this->hasMany(\App\Models\Objects\ObjectModel::class, 'idProject', 'idProject');
    }

    /************************************************
     *                    METHODS
     ************************************************/

    /**
     * Use 'idProject' as the route key.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'idProject';
    }

    /**
     * Create a new project version.
     *
     * @return \App\Models\Project\ProjectVersion
     */
    public function createVersion()
    {
        $version = $this->versions()->create([
            'idPhase'     => $this->idPhase,
            'idProject'   => $this->idProject,
            'created'     => now(),
            'historyDump' => json_encode(new ProjectResource($this)),
            'author'      => Auth::user()->username,
        ]);

        $this->update(['idLocalProject' => $version->idLocalProject]);
        
        return $version;
    }
}
