<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ObjectType
 *
 * Represents a type of object in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="ObjectType",
 *     description="ObjectType model",
 *     @OA\Property(
 *         property="idObjectType",
 *         type="integer",
 *         description="Unique identifier for the object type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the object type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the object type is hidden"
 *     )
 * )
 */
class ObjectType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeObjectTypes';

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
    protected $primaryKey = 'idObjectType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden',
    ];
}
