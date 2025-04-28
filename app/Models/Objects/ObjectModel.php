<?php

namespace App\Models\Objects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ObjectModel
 *
 * Represents an object in the system.
 *
 * @package App\Models\Objects
 *
 * @OA\Schema(
 *     schema="ObjectModel",
 *     description="ObjectModel model",
 *     @OA\Property(
 *         property="idObject",
 *         type="integer",
 *         description="Unique identifier for the object"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier of the associated project"
 *     ),
 *     @OA\Property(
 *         property="idObjectType",
 *         type="integer",
 *         description="Identifier of the associated object type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the object"
 *     )
 * )
 */
class ObjectModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'objects';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idObject';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idProject',
        'idObjectType',
        'name',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Get the project that owns the object.
     * objects.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Get the object type as an enum.
     * objects.idObjectType -> rangeObjectTypes.idObjectType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function objectType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ObjectType::class, 'idObjectType', 'idObjectType');
    }

}