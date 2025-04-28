<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class TaskStatus
 *
 * Represents a status of a task in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="TaskStatus",
 *     description="TaskStatus model",
 *     @OA\Property(
 *         property="idTaskStatus",
 *         type="integer",
 *         description="Unique identifier for the task status"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the task status"
 *     ),
 *     @OA\Property(
 *         property="isTerminal",
 *         type="boolean",
 *         description="Indicates if the task status is terminal"
 *     ),
 *     @OA\Property(
 *         property="isEnabled",
 *         type="boolean",
 *         description="Indicates if the task status is enabled"
 *     ),
 *     @OA\Property(
 *         property="rank",
 *         type="integer",
 *         description="Rank of the task status"
 *     ),
 *     @OA\Property(
 *         property="statusColor",
 *         type="string",
 *         description="Color associated with the task status"
 *     ),
 *     @OA\Property(
 *         property="statusClass",
 *         type="string",
 *         description="CSS class for the task status"
 *     )
 * )
 */
class TaskStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeTaskStatuses';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'idTaskStatus';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
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
        'name',
        'isTerminal',
        'isEnabled',
        'rank',
        'statusColor',
        'statusClass',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'isTerminal' => 'boolean',
        'isEnabled'  => 'boolean',
        'rank'       => 'integer',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * One task status is associated with many task versions (taskVersions).
     * taskVersions.idTaskStatus -> rangeTaskStatuses.idTaskStatus
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function taskVersions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskVersion::class, 'idTaskStatus', 'idTaskStatus');
    }

}
