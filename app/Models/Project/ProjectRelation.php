<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectRelation extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'projectRelations';

    /**
     * The primary key of the table.
     */
    protected $primaryKey = 'idRelation';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = true;

    /**
     * Indicates whether timestamps (created_at, updated_at) are used.
     * Your table has a 'created' column instead of the usual Laravel ones, so set this to false.
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'username',
        'idProject',
        'idRelationType',
        'idProjectRelation',
        'created',
    ];

    /**
     * Cast certain columns to native data types.
     */
    protected $casts = [
        'created' => 'datetime',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * The user who created or is associated with this relation.
     * projectRelations.username -> users.username
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * The primary project this relation belongs to.
     * projectRelations.idProject -> projects.idProject
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * The "other" related project (for example, a parent or child project).
     * projectRelations.idProjectRelation -> projects.idProject
     */
    public function relatedProject()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProjectRelation', 'idProject');
    }

    /**
     * The relation type (rangeRelationTypes table) for this record.
     * projectRelations.idRelationType -> rangeRelationTypes.idRelationType
     * If your model is stored in Enums\RelationType, reference that path:
     */
    public function relationType()
    {
        return $this->belongsTo(\App\Models\Enums\RelationType::class, 'idRelationType', 'idRelationType');
    }
}
