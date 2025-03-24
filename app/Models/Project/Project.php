<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{
    BelongsTo,
    BelongsToMany
};
use Illuminate\Support\Facades\DB;
use App\Models\Pivots\ProjectCommunication;

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
        )->using(ProjectCommunication::class)
         ->withPivot([
            'stationingFrom',
            'stationingTo',
            'gpsN1',
            'gpsN2',
            'gpsE1',
            'gpsE2',
            'allPoints',
            'geometry',
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

    /**
     * Apply filters to the query based on the request input.
     */
    public function scopeApplyFilters($query, array $filters): void
    {
        if (isset($filters['project'])) {
            $projectFilters = $filters['project'];
            if (!empty($projectFilters['id'])) {
                $query->whereIn('idProject', (array)$projectFilters['id']);
            }
            if (!empty($projectFilters['editor'])) {
                $query->whereIn('editor', (array)$projectFilters['editor']);
            }
            if (!empty($projectFilters['ou'])) {
                $query->whereHas('editorUser', function ($q) use ($projectFilters) {
                    $q->whereIn('idOu', (array)$projectFilters['ou']);
                });
            }
            if (!empty($projectFilters['type'])) {
                $query->whereIn('idProjectType', (array)$projectFilters['type']);
            }
            if (!empty($projectFilters['subtype'])) {
                $query->whereIn('idProjectSubtype', (array)$projectFilters['subtype']);
            }
            if (!empty($projectFilters['phase'])) {
                $query->whereIn('idPhase', (array)$projectFilters['phase']);
            }
            if (!empty($projectFilters['financialSource'])) {
                $query->whereIn('idFinSource', (array)$projectFilters['financialSource']);
            }
        }

        if (isset($filters['related'])) {
            $relatedFilters = $filters['related'];
            if (!empty($relatedFilters['communications'])) {
                $query->whereHas('communications', function ($q) use ($relatedFilters) {
                    $q->whereIn('project2communication.idCommunication', (array)$relatedFilters['communications']);
                });
            }
            if (!empty($relatedFilters['areas'])) {
                $query->whereHas('areas', function ($q) use ($relatedFilters) {
                    $q->whereIn('project2area.idArea', (array)$relatedFilters['areas']);
                });
            }
        }

        if (isset($filters['companies'])) {
            $companyFilters = $filters['companies'];
            if (!empty($companyFilters['supervisor'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 3)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['supervisor']);
                });
            }
            if (!empty($companyFilters['builder'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 2)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['builder']);
                });
            }
            if (!empty($companyFilters['project'])) {
                $query->whereHas('companies', function ($q) use ($companyFilters) {
                    $q->where('idCompanyType', 1)
                      ->whereIn('project2company.idCompany', (array)$companyFilters['project']);
                });
            }
        }
    }

    /**
     * Apply sorting to the query based on the request input.
     */
    public function scopeApplySorting($query, string $sortField, string $sortOrder): void
    {
        $fieldMap = [
            'idProject' => 'idProject',
            'created' => 'created',
            'editor' => 'editor',
            'name' => 'name',
        ];

        $sortField = $fieldMap[$sortField] ?? $sortField;
        $query->orderBy($sortField, $sortOrder);
    }

    /**
     * Define the relationships to be eager-loaded.
     */
    public function scopeWithRelationships($query): void
    {
        $query->with([
            'projectType:idProjectType,name',
            'phase:idPhase,name,phaseColor,phaseColorClass',
            'editorUser:username,name',
            'financialSource:idFinSource,name',
            'areas:idArea,name',
            'communications'
        ]);
    }
}
