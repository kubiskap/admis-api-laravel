<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 *
 * Represents a notification for a user.
 *
 * @package App\Models\Users
 *
 * @OA\Schema(
 *     schema="Notification",
 *     description="Notification model",
 *     @OA\Property(
 *         property="idNotification",
 *         type="integer",
 *         description="Unique identifier for the notification"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the user to whom the notification belongs"
 *     ),
 *     @OA\Property(
 *         property="idAction",
 *         type="integer",
 *         description="Identifier for the action log that this notification refers to"
 *     ),
 *     @OA\Property(
 *         property="viewed",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the notification was viewed"
 *     )
 * )
 */
class Notification extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'notifications';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'idNotification';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should manage created_at/updated_at timestamps.
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
        'username',
        'idAction',
        'viewed',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'viewed' => 'datetime',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * The user this notification belongs to.
     * notifications.username -> users.username
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * The action log this notification refers to.
     * notifications.idAction -> actionsLogs.idAction 
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function actionLog()
    {
        return $this->belongsTo(\App\Models\Logs\ActionLog::class, 'idAction', 'idAction');
    }
}
