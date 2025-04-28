<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskVersion
 *
 * Represents a version of a task.
 *
 * @package App\Models\Tasks
 *
 * @OA\Schema(
 *     schema="TaskVersion",
 *     description="TaskVersion model",
 *     @OA\Property(
 *         property="idTask",
 *         type="integer",
 *         description="Identifier for the task (foreign key to tasksProject)"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the task version"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the task version"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the task version was created"
 *     ),
 *     @OA\Property(
 *         property="createdBy",
 *         type="string",
 *         description="Username of the user who created the task version"
 *     ),
 *     @OA\Property(
 *         property="idTaskStatus",
 *         type="integer",
 *         description="Identifier for the current status of the task version (foreign key to rangeTaskStatuses)"
 *     ),
 *     @OA\Property(
 *         property="deadlineTo",
 *         type="string",
 *         format="date-time",
 *         description="Deadline for the task version"
 *     )
 * )
 */
class TaskVersion extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'taskVersions';

    /**
     * The primary key is composite, so set this to null.
     *
     * @var null
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * Disables automatic timestamps (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'idTask',
        'name',
        'description',
        'created',
        'createdBy',
        'idTaskStatus',
        'deadlineTo',
    ];

    /**
     * Cast attributes to native types.
     *
     * @var array
     */
    protected $casts = [
        'created'    => 'datetime',
        'deadlineTo' => 'datetime',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Each TaskVersion belongs to a Task.
     * taskVersions.idTask -> tasksProject.idTask
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\Task::class, 'idTask', 'idTask');
    }

    /**
     * Each TaskVersion has a status.
     * taskVersions.idTaskStatus -> rangeTaskStatuses.idTaskStatus
     *
     * @return BelongsTo
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\TaskStatus::class, 'idTaskStatus', 'idTaskStatus');
    }

    /**
     * Each TaskVersion is created by a user.
     * taskVersions.createdBy -> users.username
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }
}
