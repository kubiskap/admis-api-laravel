<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectVersion extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'projectVersions';

    /**
     * The primary key for the table.
     *
     * According to your schema, `idLocalProject` is the primary key
     * and is also auto-incrementing.
     */
    protected $primaryKey = 'idLocalProject';

    /**
     * Indicates if the primary key is auto-incrementing.
     * (Check your schema if `AUTO_INCREMENT` is set on `idLocalProject`.)
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped (created_at, updated_at).
     * The table has a `created` column, but not the usual Laravel timestamps, so set this to false.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
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
     */
    protected $casts = [
        'created' => 'datetime',
        'validTo' => 'datetime',
        'historyDump' => 'json',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Relation to Project (projectVersions.idProject -> projects.idProject).
     * If you have a `Project` model in `App\Models\Project`, reference that path.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Each ProjectVersion can have many ActionLogs
     */
    public function actionLogs(): HasMany
    {
        return $this->hasMany(\App\Models\Logs\ActionLog::class, 'idLocalProject', 'idLocalProject');
    }

    /**
     * Relation to Phase (projectVersions.idPhase -> rangePhases.idPhase),
     * if you want to reference the phase range table (optional).
     */
    public function phase(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Phase::class, 'idPhase', 'idPhase');
    }
}
