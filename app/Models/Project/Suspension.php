<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Suspension
 *
 * Represents a suspension record for a project.
 *
 * @package App\Models\Project
 *
 * @OA\Schema(
 *     schema="Suspension",
 *     description="Suspension model",
 *     @OA\Property(
 *         property="idSuspension",
 *         type="integer",
 *         description="Unique identifier for the suspension"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the associated project"
 *     ),
 *     @OA\Property(
 *         property="idSuspensionSource",
 *         type="integer",
 *         description="Identifier for the suspension source"
 *     ),
 *     @OA\Property(
 *         property="idSuspensionReason",
 *         type="integer",
 *         description="Identifier for the suspension reason"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="Comment or note regarding the suspension"
 *     ),
 *     @OA\Property(
 *         property="dateFrom",
 *         type="string",
 *         format="date",
 *         description="Start date of the suspension"
 *     ),
 *     @OA\Property(
 *         property="dateTo",
 *         type="string",
 *         format="date",
 *         description="End date of the suspension"
 *     ),
 *     @OA\Property(
 *         property="deleted",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the suspension was deleted"
 *     ),
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Username of the user who recorded the suspension"
 *     )
 * )
 */
class Suspension extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'suspensions';

    /**
     * The primary key of the table.
     *
     * @var string
     */
    protected $primaryKey = 'idSuspension';

    /**
     * Indicates if the primary key is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should manage timestamps.
     * This model does not use the standard Laravel timestamps.
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
     *
     * @var array
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Belongs to a Suspension Source (suspensions.idSuspensionSource -> rangeSuspensionSources.idSuspensionSource).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suspensionSource()
    {
        return $this->belongsTo(\App\Models\Enums\SuspensionSource::class, 'idSuspensionSource', 'idSuspensionSource');
    }

    /**
     * Belongs to a Suspension Reason (suspensions.idSuspensionReason -> rangeSuspensionReasons.idSuspensionReason).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function suspensionReason()
    {
        return $this->belongsTo(\App\Models\Enums\SuspensionReason::class, 'idSuspensionReason', 'idSuspensionReason');
    }

    /**
     * Belongs to a User (suspensions.username -> users.username).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }
}