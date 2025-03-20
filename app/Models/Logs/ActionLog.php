<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'actionsLogs';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idAction';

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
        'idActionType',
        'idLocalProject',
        'username',
        'created'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'datetime',
    ];

    /**
     * Get the action type that owns the log entry.
     */
    public function actionType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ActionType::class, 'idActionType', 'idActionType');
    }

    /**
     * Get the user that owns the log entry.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * Get the project version associated with the log entry.
     * Note: This assumes you have a ProjectVersion model.
     */
    public function projectVersion(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\ProjectVersion::class, 'idLocalProject', 'idLocalProject');
    }
}
