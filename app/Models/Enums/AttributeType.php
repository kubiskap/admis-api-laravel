<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AttributeType
 *
 * Represents a specific type of attribute in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="AttributeType",
 *     description="AttributeType model",
 *     @OA\Property(
 *         property="idAttributeType",
 *         type="integer",
 *         description="Unique identifier for the attribute type"
 *     ),
 *     @OA\Property(
 *         property="idAttGroup",
 *         type="integer",
 *         description="Identifier of the related attribute group"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the attribute type"
 *     ),
 *     @OA\Property(
 *         property="type",
 *         type="string",
 *         description="Data type of the attribute"
 *     ),
 *     @OA\Property(
 *         property="ordering",
 *         type="integer",
 *         description="Ordering position of the attribute type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the attribute type is hidden"
 *     )
 * )
 */
class AttributeType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeAttributeTypes';

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
    protected $primaryKey = 'idAttributeType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idAttGroup',
        'name',
        'type',
        'ordering',
        'hidden',
    ];
}
