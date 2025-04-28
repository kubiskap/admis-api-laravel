<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProjectTypeProjectSubtype
 *
 * Represents the pivot linking project types and project subtypes with an associated priority configuration.
 *
 * @package App\Models\Pivots
 *
 * @OA\Schema(
 *     schema="ProjectTypeProjectSubtype",
 *     description="Pivot model linking project types and subtypes with a priority configuration",
 *     @OA\Property(
 *         property="idProjectType",
 *         type="integer",
 *         description="Identifier for the project type"
 *     ),
 *     @OA\Property(
 *         property="idProjectSubtype",
 *         type="integer",
 *         description="Identifier for the project subtype"
 *     ),
 *     @OA\Property(
 *         property="idPriorityConfig",
 *         type="integer",
 *         description="Identifier for the priority configuration"
 *     )
 * )
 */
class ProjectTypeProjectSubtype extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'type2subtype';

    /**
     * Indicates that this pivot table does not have an auto-incrementing primary key.
     * The primary key is composite: (idProjectType, idProjectSubtype).
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Disable timestamps (created_at, updated_at) for this pivot.
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
        'idProjectType',
        'idProjectSubtype',
        'idPriorityConfig',
    ];

    /**
     * Cast specified fields to native types.
     *
     * @var array
     */
    protected $casts = [
        'idProjectType'    => 'integer',
        'idProjectSubtype' => 'integer',
        'idPriorityConfig' => 'integer',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Relationship to the PriorityScaleConfig model.
     * type2subtype.idPriorityConfig -> rangePriorityScaleConfig.idPriorityConfig
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priorityConfig(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\PriorityScaleConfig::class, 'idPriorityConfig', 'idPriorityConfig');
    }
}