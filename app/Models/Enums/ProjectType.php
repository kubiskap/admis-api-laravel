<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ProjectType
 *
 * Represents a type of project in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="ProjectType",
 *     description="ProjectType model",
 *     @OA\Property(
 *         property="idProjectType",
 *         type="integer",
 *         description="Unique identifier for the project type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the project type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the project type is hidden"
 *     )
 * )
 */
class ProjectType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeProjectTypes';

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
    protected $primaryKey = 'idProjectType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Many project types belong to many project subtypes (type2subtype).
     * projectType.idProjectType -> type2subtype.idProjectType
     * type2subtype.idProjectSubtype -> projectSubtype.idProjectSubtype
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subtypes(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\ProjectSubtype::class,
            'type2subtype',
            'idProjectType',
            'idProjectSubtype'
        )
        ->using(\App\Models\Pivots\ProjectTypeProjectSubtype::class)
        ->withPivot('idPriorityConfig');
    }
}
