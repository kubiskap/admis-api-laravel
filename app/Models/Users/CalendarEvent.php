<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'calendarEvents';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'idEvent';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true; // The schema sets an AUTO_INCREMENT on idEvent

    /**
     * Indicates if the model should be timestamped (created_at, updated_at).
     * The table has 'created' and 'deletedTimestamp' columns, not
     * the default Laravel timestamp columns, so we set this false.
     */
    public $timestamps = false;

    /**
     * The attributes that can be assigned in bulk.
     */
    protected $fillable = [
        'username',
        'title',
        'description',
        'eventStart',
        'eventEnd',
        'deleted',
        'created',
        'deletedTimestamp',
        'idOu',
        'deletedAuthor',
        'idEventUpdated',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'deleted' => 'boolean',
        'eventStart' => 'datetime',
        'eventEnd' => 'datetime',
        'created' => 'datetime',
        'deletedTimestamp' => 'datetime',
    ];

    /************************************************
     *                   RELATIONSHIPS
     ************************************************/

    /**
     * Many calendar events belong to one user (calendarEvents.username -> users.username).
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * If you want to track who deleted the event (deletedAuthor -> users.username).
     * This is optional unless you have a foreign key set or logically want the relation.
     */
    public function deletedAuthorUser()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedAuthor', 'username');
    }

    /**
     * If you want to link to the 'ou' table (calendarEvents.idOu -> ou.idOu).
     * This also is optional if there's no real foreign key or usage in your app.
     */
    public function ou()
    {
        return $this->belongsTo(\App\Models\Enums\Ou::class, 'idOu', 'idOu');
    }

    /************************************************
     *              EXAMPLE SCOPES / METHODS
     ************************************************/

    /**
     * Scope to retrieve only active (non-deleted) events.
     */
    public function scopeActive($query)
    {
        return $query->where('deleted', false);
    }

    /**
     * Mark an event as deleted, if you aren't physically removing the row.
     */
    public function softDelete()
    {
        $this->deleted = true;
        $this->deletedTimestamp = now();
        $this->save();
    }
}
