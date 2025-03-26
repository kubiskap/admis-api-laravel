<?php

namespace App\Models\Tasks;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Task extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'tasksProject';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'idTask';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = true;

    /**
     * Disables Laravel's automatic timestamp columns (created_at, updated_at).
     */
    public $timestamps = false;

    /**
     * Fields that can be mass assigned.
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
     */
    protected $casts = [
        'created'    => 'datetime',
        'deleted'    => 'datetime',
        'privateTask' => 'boolean',
    ];

    /**
     * If you want to link each task to its project (projects.idProject).
     * tasksProject.relatedToProjectId -> projects.idProject
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'relatedToProjectId', 'idProject');
    }

    /**
     * A task can have many versions (taskVersions).
     * tasksProject.idTask -> taskVersions.idTask
     */
    public function versions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskVersion::class, 'idTask', 'idTask');
    }

    public function latestVersion(): HasOne
    {
        return $this->hasOne(\App\Models\Tasks\TaskVersion::class, 'idTask', 'idTask')
                    ->latest('created');
    }

    /**
     * If you want to link 'createdBy' to a User model (users.username).
     * tasksProject.createdBy -> users.username
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'createdBy', 'username');
    }

    /**
     * If you want to link 'deletedBy' to a User model (users.username).
     * tasksProject.deletedBy -> users.username
     */
    public function deletor(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedBy', 'username');
    }

    /**
     * A task can have many reactions.
     * tasksProject.idTask -> taskReactions.idTask
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskReaction::class, 'idTask', 'idTask');
    }
}
