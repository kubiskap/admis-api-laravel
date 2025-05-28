<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CalendarEvent
 *
 * Represents a calendar event for a user.
 *
 * @package App\Models\Users
 *
 * @OA\Schema(
 *     schema="CalendarEvent",
 *     description="CalendarEvent model",
 *     @OA\Property(
 *         property="idEvent",
 *         type="integer",
 *         description="Unique identifier for the calendar event"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the owner of the event"
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Title of the event"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the event"
 *     ),
 *     @OA\Property(
 *         property="eventStart",
 *         type="string",
 *         format="date-time",
 *         description="Start time of the event"
 *     ),
 *     @OA\Property(
 *         property="eventEnd",
 *         type="string",
 *         format="date-time",
 *         description="End time of the event"
 *     ),
 *     @OA\Property(
 *         property="deleted",
 *         type="boolean",
 *         description="Indicates if the event is deleted"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the event was created"
 *     ),
 *     @OA\Property(
 *         property="deletedTimestamp",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the event was deleted"
 *     ),
 *     @OA\Property(
 *         property="idOu",
 *         type="integer",
 *         description="Identifier for the organizational unit associated with the event"
 *     ),
 *     @OA\Property(
 *         property="deletedAuthor",
 *         type="string",
 *         description="Username of the user who deleted the event"
 *     ),
 *     @OA\Property(
 *         property="idEventUpdated",
 *         type="integer",
 *         description="Identifier of the event update"
 *     )
 * )
 */
class CalendarEvent extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calendarEvents';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'idEvent';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true; // The schema sets AUTO_INCREMENT on idEvent

    /**
     * Disables Laravel's default timestamp columns; this table uses custom ones.
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
     *
     * @var array
     */
    protected $casts = [
        'deleted'         => 'boolean',
        'eventStart'      => 'datetime',
        'eventEnd'        => 'datetime',
        'created'         => 'datetime',
        'deletedTimestamp'=> 'datetime',
    ];

    /************************************************
     *                   RELATIONSHIPS
     ************************************************/

    /**
     * Many calendar events belong to one user (calendarEvents.username -> users.username).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * Optionally retrieve the user who deleted the event (deletedAuthor -> users.username).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deletedAuthorUser()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'deletedAuthor', 'username');
    }

    /**
     * Optionally retrieve the organizational unit associated with the event (idOu -> ou.idOu).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('deleted', false);
    }

    /**
     * Mark an event as deleted without physically removing it.
     */
    public function softDelete()
    {
        $this->deleted = true;
        $this->deletedTimestamp = now();
        $this->save();
    }
}
