<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskVersion extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'taskVersions';

    /**
     * The primary key is a composite of (idTask, created).
     * Eloquent doesn't natively handle composite keys,
     * so we set $primaryKey to null and disable incrementing.
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * The table doesn't use created_at / updated_at columns,
     * so we disable automatic timestamps.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
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
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'created'    => 'datetime',
        'deadlineTo' => 'datetime',
    ];

    /**
     * Each taskVersion belongs to a Task (tasksProject).
     * taskVersions.idTask -> tasksProject.idTask
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Tasks\Task::class, 'idTask', 'idTask');
    }

    /**
     * If idTaskStatus references rangeTaskStatuses.idTaskStatus,
     * define a relationship here. For example:
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\TaskStatus::class, 'idTaskStatus', 'idTaskStatus');
    }

    /**
     * If createdBy references users.username, add a belongsTo:
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }
}
