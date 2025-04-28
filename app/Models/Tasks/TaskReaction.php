<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class TaskReaction
 *
 * Represents a reaction to a task.
 *
 * @package App\Models\Tasks
 *
 * @OA\Schema(
 *     schema="TaskReaction",
 *     description="TaskReaction model",
 *     @OA\Property(
 *         property="idTask",
 *         type="integer",
 *         description="Identifier for the task that this reaction belongs to"
 *     ),
 *     @OA\Property(
 *         property="reaction",
 *         type="string",
 *         description="The reaction content or type"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the reaction was created"
 *     ),
 *     @OA\Property(
 *         property="createdBy",
 *         type="string",
 *         description="Username of the user who created the reaction"
 *     ),
 *     @OA\Property(
 *         property="deleted",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the reaction was deleted, if applicable"
 *     ),
 *     @OA\Property(
 *         property="deletedBy",
 *         type="string",
 *         description="Username of the user who deleted the reaction, if applicable"
 *     )
 * )
 */
class TaskReaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'taskReactions';

    /**
     * Since the table has a composite primary key, set primaryKey to null.
     *
     * @var null
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * The table doesn't use automatic timestamps.
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
        'reaction',
        'created',
        'createdBy',
        'deleted',
        'deletedBy',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'datetime',
        'deleted' => 'datetime',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Each taskReaction belongs to a Task (tasksProject).
     * taskReactions.idTask -> tasksProject.idTask
     *
     * @return BelongsTo
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\Task::class, 'idTask', 'idTask');
    }

    /**
     * If 'createdBy' references a user (users.username),
     * this relationship retrieves the creator of the reaction.
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }

    /**
     * If 'deletedBy' references a user (users.username),
     * this relationship retrieves the user who deleted the reaction.
     *
     * @return BelongsTo
     */
    public function deletor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedBy', 'username');
    }
}
