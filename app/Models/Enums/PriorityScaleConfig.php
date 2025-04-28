<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriorityScaleConfig
 *
 * Represents the configuration for priority scales in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="PriorityScaleConfig",
 *     description="PriorityScaleConfig model",
 *     @OA\Property(
 *         property="idPriorityConfig",
 *         type="integer",
 *         description="Unique identifier for the priority scale configuration"
 *     ),
 *     @OA\Property(
 *         property="configJson",
 *         type="string",
 *         description="JSON configuration for the priority scale"
 *     )
 * )
 */
class PriorityScaleConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangePriorityScaleConfig';

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
    protected $primaryKey = 'idPriorityConfig';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'configJson',
    ];
}