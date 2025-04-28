<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class DeadlineType
 *
 * Represents a type of deadline in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="DeadlineType",
 *     description="DeadlineType model",
 *     @OA\Property(
 *         property="idDeadlineType",
 *         type="integer",
 *         description="Unique identifier for the deadline type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the deadline type"
 *     ),
 *     @OA\Property(
 *         property="nameEn",
 *         type="string",
 *         description="English name of the deadline type"
 *     ),
 *     @OA\Property(
 *         property="availableInPhase",
 *         type="string",
 *         description="Phase in which the deadline type is available"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the deadline type is hidden"
 *     )
 * )
 */
class DeadlineType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeDeadlineTypes';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'idDeadlineType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nameEn',
        'availableInPhase',
        'hidden',
    ];
}