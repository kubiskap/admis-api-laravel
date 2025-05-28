<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Deadline
 *
 * Represents a project deadline.
 *
 * @package App\Models\Project
 *
 * @OA\Schema(
 *     schema="Deadline",
 *     description="Deadline model",
 *     @OA\Property(
 *         property="idDeadline",
 *         type="integer",
 *         description="Unique identifier for the deadline"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the associated project"
 *     ),
 *     @OA\Property(
 *         property="idDeadlineType",
 *         type="integer",
 *         description="Identifier for the deadline type"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="string",
 *         format="date",
 *         description="Deadline value as a date"
 *     ),
 *     @OA\Property(
 *         property="note",
 *         type="string",
 *         description="Additional note for the deadline"
 *     ),
 *     @OA\Property(
 *         property="inserted",
 *         type="string",
 *         format="date",
 *         description="Date when the deadline was inserted"
 *     )
 * )
 */
class Deadline extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'deadlines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProject',
        'idDeadlineType',
        'value',
        'note',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    public $casts = [
        'value'    => 'datetime:Y-m-d',
        'inserted' => 'date',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Get the associated project.
     * deadlines.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Get the deadline type.
     * deadlines.idDeadlineType -> deadlineTypes.idDeadlineType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deadlineType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\DeadlineType::class, 'idDeadlineType', 'idDeadlineType');
    }
}
