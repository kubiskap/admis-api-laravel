<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class ProjectRelation
 *
 * Represents a relation between projects (e.g., parent or child relationship).
 *
 * @package App\Models\Pivots
 *
 * @OA\Schema(
 *     schema="ProjectRelation",
 *     description="ProjectRelation pivot model",
 *     @OA\Property(
 *         property="idRelation",
 *         type="integer",
 *         description="Unique identifier for the relation"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the user who created the relation"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier of the source project"
 *     ),
 *     @OA\Property(
 *         property="idRelationType",
 *         type="integer",
 *         description="Identifier for the relation type"
 *     ),
 *     @OA\Property(
 *         property="idProjectRelation",
 *         type="integer",
 *         description="Identifier for the related project"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the relation was created"
 *     )
 * )
 */
class ProjectRelation extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projectRelations';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'idRelation';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates whether timestamps are used.
     * Note: This table uses a 'created' column instead of the default Laravel timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'idProject',
        'idRelationType',
        'idProjectRelation',
        'created',
    ];

    /**
     * Cast certain columns to native types.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'datetime',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * The relation type (rangeRelationTypes table) for this record.
     * projectRelations.idRelationType -> rangeRelationTypes.idRelationType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relationType()
    {
        return $this->belongsTo(\App\Models\Enums\RelationType::class, 'idRelationType', 'idRelationType');
    }

    /**
     * The related project (e.g., parent or child project).
     * projectRelations.idProjectRelation -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function relatedProject()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProjectRelation', 'idProject');
    }

}
