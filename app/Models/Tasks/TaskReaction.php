<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskReaction extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'taskReactions';

    /**
     * The table has a composite primary key: (idTask, created).
     * Eloquent doesn't handle composite keys natively, so set primaryKey = null
     * and incrementing = false to allow read/write but not standard find().
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * The table doesn't have created_at / updated_at columns,
     * so we disable default timestamp management.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
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
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\Task::class, 'idTask', 'idTask');
    }

    /**
     * If 'createdBy' references a user (users.username),
     * we can define this relationship as well:
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }

    /**
     * If 'deletedBy' references a user (users.username),
     * we can define this relationship as well:
     */
    public function deletor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedBy', 'username');
    }
}
