<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class ProjectVersion
 *
 * Represents a version of a project.
 *
 * @package App\Models\Project
 *
 * @OA\Schema(
 *     schema="ProjectVersion",
 *     description="ProjectVersion model",
 *     @OA\Property(
 *         property="idLocalProject",
 *         type="integer",
 *         description="Unique identifier for the project version"
 *     ),
 *     @OA\Property(
 *         property="idPhase",
 *         type="integer",
 *         description="Identifier for the phase associated with this project version"
 *     ),
 *     @OA\Property(
 *         property="assignments",
 *         type="string",
 *         description="Assignments data related to this project version (if applicable)"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the parent project"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the project version was created"
 *     ),
 *     @OA\Property(
 *         property="validTo",
 *         type="string",
 *         format="date-time",
 *         description="Expiration timestamp for this project version"
 *     ),
 *     @OA\Property(
 *         property="historyDump",
 *         type="object",
 *         description="JSON dump of the project history"
 *     ),
 *     @OA\Property(
 *         property="author",
 *         type="string",
 *         description="Author of the project version"
 *     )
 * )
 */
class ProjectVersion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'projectVersions';

    /**
     * The primary key for the table.
     * According to your schema, `idLocalProject` is the primary key and auto-incrementing.
     *
     * @var string
     */
    protected $primaryKey = 'idLocalProject';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     * The table has a `created` column but not the standard Laravel timestamps.
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
        'idPhase',
        'assignments',
        'idProject',
        'created',
        'validTo',
        'historyDump',
        'author'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created'     => 'datetime',
        'validTo'     => 'datetime',
        'historyDump' => 'json',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Relation to the parent Project.
     * projectVersions.idProject -> projects.idProject
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Each ProjectVersion can have many ActionLogs.
     *
     * @return HasMany
     */
    public function actionLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Logs\ActionLog::class, 'idLocalProject', 'idLocalProject');
    }

    /**
     * Relation to Phase.
     * projectVersions.idPhase -> rangePhases.idPhase
     *
     * @return BelongsTo
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Phase::class, 'idPhase', 'idPhase');
    }
}
