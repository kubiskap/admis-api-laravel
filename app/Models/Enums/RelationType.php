<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RelationType
 *
 * Represents a type of relation in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="RelationType",
 *     description="RelationType model",
 *     @OA\Property(
 *         property="idRelationType",
 *         type="integer",
 *         description="Unique identifier for the relation type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the relation type"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the relation type"
 *     ),
 *     @OA\Property(
 *         property="relationFromProjectRelation",
 *         type="boolean",
 *         description="Indicates if the relation originates from a project relation"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the relation type is hidden"
 *     )
 * )
 */
class RelationType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeRelationTypes';

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
    protected $primaryKey = 'idRelationType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'relationFromProjectRelation',
        'hidden',
    ];
}
