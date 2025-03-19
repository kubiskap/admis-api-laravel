<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

class Suspension extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'suspensions';

    /**
     * The primary key of the table.
     */
    protected $primaryKey = 'idSuspension';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = true;

    /**
     * Indicates if the model should manage created_at/updated_at timestamps.
     * Your table does not have standard Laravel timestamps, so set false.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     */
    protected $fillable = [
        'idProject',
        'idSuspensionSource',
        'idSuspensionReason',
        'comment',
        'dateFrom',
        'dateTo',
        'deleted',
        'username',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'dateFrom' => 'date',
        'dateTo'   => 'date',
        'deleted'  => 'datetime',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Belongs to a Project (suspensions.idProject -> projects.idProject).
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Belongs to a suspension source (rangeSuspensionSources).
     * suspensions.idSuspensionSource -> rangeSuspensionSources.idSuspensionSource
     */
    public function suspensionSource()
    {
        return $this->belongsTo(\App\Models\Enums\SuspensionSource::class, 'idSuspensionSource', 'idSuspensionSource');
    }

    /**
     * Belongs to a suspension reason (rangeSuspensionReasons).
     * suspensions.idSuspensionReason -> rangeSuspensionReasons.idSuspensionReason
     */
    public function suspensionReason()
    {
        return $this->belongsTo(\App\Models\Enums\SuspensionReason::class, 'idSuspensionReason', 'idSuspensionReason');
    }

    /**
     * Belongs to a user (suspensions.username -> users.username).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }
}