<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'notifications';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'idNotification';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = true;

    /**
     * Indicates if the model should manage created_at/updated_at timestamps.
     * Your table has none, so we disable it.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     */
    protected $fillable = [
        'username',
        'idAction',
        'viewed',
    ];

    /**
     * The attributes that should be cast to native types.
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
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * The action log this notification refers to.
     * notifications.idAction -> actionsLogs.idAction 
     */
    public function actionLog()
    {
        return $this->belongsTo(\App\Models\Logs\ActionLog::class, 'idAction', 'idAction');
    }
}
