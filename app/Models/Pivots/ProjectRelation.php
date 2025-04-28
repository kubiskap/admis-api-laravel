<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProjectRelation extends Pivot
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
     * The relation type (rangeRelationTypes table) for this record.
     */
    public function relationType()
    {
        return $this->belongsTo(\App\Models\Enums\RelationType::class, 'idRelationType', 'idRelationType');
    }

    /**
     * The related project (e.g., parent or child project).
     */
    public function relatedProject()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProjectRelation', 'idProject');
    }
}
