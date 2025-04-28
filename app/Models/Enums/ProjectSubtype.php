<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class ProjectSubtype
 *
 * Represents a subtype of a project in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="ProjectSubtype",
 *     description="ProjectSubtype model",
 *     @OA\Property(
 *         property="idProjectSubtype",
 *         type="integer",
 *         description="Unique identifier for the project subtype"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the project subtype"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the project subtype is hidden"
 *     )
 * )
 */
class ProjectSubtype extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeProjectSubtypes';

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
    protected $primaryKey = 'idProjectSubtype';

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
     * Many project subtypes belong to many project types (type2subtype).
     * projectSubtype.idProjectSubtype -> type2subtype.idProjectSubtype
     * type2subtype.idProjectType -> projectType.idProjectType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function types(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\ProjectType::class,
            'type2subtype',
            'idProjectSubtype',
            'idProjectType'
        )
        ->using(\App\Models\Pivots\ProjectTypeProjectSubtype::class)
        ->withPivot('idPriorityConfig');
    }
}