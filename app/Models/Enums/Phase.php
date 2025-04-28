<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Phase
 *
 * Represents a phase in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Phase",
 *     description="Phase model",
 *     @OA\Property(
 *         property="idPhase",
 *         type="integer",
 *         description="Unique identifier for the phase"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the phase"
 *     ),
 *     @OA\Property(
 *         property="nameEn",
 *         type="string",
 *         description="English name of the phase"
 *     ),
 *     @OA\Property(
 *         property="level",
 *         type="integer",
 *         description="Level of the phase"
 *     ),
 *     @OA\Property(
 *         property="phasing",
 *         type="string",
 *         description="Phasing details of the phase"
 *     ),
 *     @OA\Property(
 *         property="phaseColor",
 *         type="string",
 *         description="Color associated with the phase"
 *     ),
 *     @OA\Property(
 *         property="phaseColorClass",
 *         type="string",
 *         description="CSS class for the phase color"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the phase is hidden"
 *     ),
 *     @OA\Property(
 *         property="phaseForLiteProject",
 *         type="boolean",
 *         description="Indicates if the phase is for lite projects"
 *     )
 * )
 */
class Phase extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangePhases';

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
    protected $primaryKey = 'idPhase';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nameEn',
        'level',
        'phasing',
        'phaseColor',
        'phaseColorClass',
        'hidden',
        'phaseForLiteProject',
    ];
}
