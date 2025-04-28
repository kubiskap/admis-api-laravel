<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Collaborator
 *
 * Represents a collaborator relationship between users.
 *
 * @package App\Models\Users
 *
 * @OA\Schema(
 *     schema="Collaborator",
 *     description="Collaborator model linking a user with their assigned collaborator",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="Unique identifier for the collaborator record"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the user who owns or created the collaborator relationship"
 *     ),
 *     @OA\Property(
 *         property="collaborator",
 *         type="string",
 *         description="Username of the collaborator assigned to the user"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the collaborator relationship was created"
 *     ),
 *     @OA\Property(
 *         property="begin",
 *         type="string",
 *         format="date-time",
 *         description="Start time of the collaboration period"
 *     ),
 *     @OA\Property(
 *         property="expiry",
 *         type="string",
 *         format="date-time",
 *         description="Expiry time of the collaborator relationship"
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         description="Indicates whether the collaborator relationship is currently active"
 *     )
 * )
 */
class Collaborator extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'collaborator';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Disables Laravel's default timestamp columns; this table uses a 'created' column.
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
        'collaborator',
        'created',
        'begin',
        'expiry',
        'active',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created'  => 'datetime',
        'begin'    => 'datetime',
        'expiry'   => 'datetime',
        'active'   => 'boolean',
    ];

    /************************************************
     *                   RELATIONSHIPS
     ************************************************/

    /**
     * The user who owns or created the collaborator relationship.
     * collaborator.username -> users.username
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * The collaborator assigned to the user.
     * collaborator.collaborator -> users.username
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function collaboratorUser()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'collaborator', 'username');
    }

    /************************************************
     *               CUSTOM METHODS
     ************************************************/

    /**
     * Scope to retrieve only active collaborator relationships.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Mark this collaborator relationship as inactive.
     */
    public function deactivate()
    {
        $this->active = false;
        $this->save();
    }
}
