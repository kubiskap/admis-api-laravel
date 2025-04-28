<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Task
 *
 * Represents a task within a project.
 *
 * @package App\Models\Tasks
 *
 * @OA\Schema(
 *     schema="Task",
 *     description="Task model",
 *     @OA\Property(
 *         property="idTask",
 *         type="integer",
 *         description="Unique identifier for the task"
 *     ),
 *     @OA\Property(
 *         property="createdBy",
 *         type="string",
 *         description="Username of the user who created the task"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the task was created"
 *     ),
 *     @OA\Property(
 *         property="deletedBy",
 *         type="string",
 *         description="Username of the user who deleted the task"
 *     ),
 *     @OA\Property(
 *         property="deleted",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the task was deleted"
 *     ),
 *     @OA\Property(
 *         property="relatedToProjectId",
 *         type="integer",
 *         description="Identifier of the project to which this task belongs"
 *     ),
 *     @OA\Property(
 *         property="privateTask",
 *         type="boolean",
 *         description="Indicates if the task is private"
 *     )
 * )
 */
class Task extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tasksProject';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'idTask';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Disables Laravel's automatic timestamp columns (created_at, updated_at).
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'createdBy',
        'created',
        'deletedBy',
        'deleted',
        'relatedToProjectId',
        'privateTask',
    ];

    /**
     * Cast certain columns to native types.
     *
     * @var array
     */
    protected $casts = [
        'created'     => 'datetime',
        'deleted'     => 'datetime',
        'privateTask' => 'boolean',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * If you want to link each task to its project.
     * tasksProject.relatedToProjectId -> projects.idProject
     *
     * @return BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'relatedToProjectId', 'idProject');
    }

    /**
     * A task can have many versions.
     * tasksProject.idTask -> taskVersions.idTask
     *
     * @return HasMany
     */
    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskVersion::class, 'idTask', 'idTask');
    }

    /**
     * Get the latest version of the task.
     *
     * @return HasOne
     */
    public function latestVersion(): HasOne
    {
        return $this->hasOne(\App\Models\Tasks\TaskVersion::class, 'idTask', 'idTask')
                    ->latest('created');
    }

    /**
     * Link 'createdBy' to a User model.
     * tasksProject.createdBy -> users.username
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }

    /**
     * Link 'deletedBy' to a User model.
     * tasksProject.deletedBy -> users.username
     *
     * @return BelongsTo
     */
    public function deletor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedBy', 'username');
    }

    /**
     * A task can have many reactions.
     * tasksProject.idTask -> taskReactions.idTask
     *
     * @return HasMany
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskReaction::class, 'idTask', 'idTask');
    }
}
