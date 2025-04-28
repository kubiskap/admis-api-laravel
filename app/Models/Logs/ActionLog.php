<?php

namespace App\Models\Logs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ActionLog
 *
 * Represents an action log entry in the system.
 *
 * @package App\Models\Logs
 *
 * @OA\Schema(
 *     schema="ActionLog",
 *     description="ActionLog model",
 *     @OA\Property(
 *         property="idAction",
 *         type="integer",
 *         description="Unique identifier for the action log entry"
 *     ),
 *     @OA\Property(
 *         property="idActionType",
 *         type="integer",
 *         description="Identifier of the associated action type"
 *     ),
 *     @OA\Property(
 *         property="idLocalProject",
 *         type="integer",
 *         description="Identifier of the associated local project"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the user who performed the action"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the action was logged"
 *     )
 * )
 */
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
        'created',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created' => 'datetime',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Get the action type that owns the log entry.
     * actionsLogs.idActionType -> rangeActionTypes.idActionType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actionType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ActionType::class, 'idActionType', 'idActionType');
    }

    /**
     * Get the user that owns the log entry.
     * actionsLogs.username -> users.username
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * Get the project version associated with the log entry.
     * actionsLogs.idLocalProject -> projectVersions.idLocalProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function projectVersion(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\ProjectVersion::class, 'idLocalProject', 'idLocalProject');
    }

    /************************************************
     *                    METHODS
     ************************************************/

    /**
     * Log an action.
     *
     * @param int $actionTypeId The ID of the action type.
     * @param int $localProjectId The ID of the local project.
     * @return ActionLog
     */
    public static function logAction(int $actionTypeId, int $localProjectId): self
    {
        return self::create([
            'idActionType' => $actionTypeId,
            'idLocalProject' => $localProjectId,
            'username' => \Illuminate\Support\Facades\Auth::user()->username, // Automatically use the authenticated user's username
            'created' => now(), // Automatically set the current timestamp
        ]);
    }
}
