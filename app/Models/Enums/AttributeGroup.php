<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AttributeGroup
 *
 * Represents a group of attributes in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="AttributeGroup",
 *     description="AttributeGroup model",
 *     @OA\Property(
 *         property="idAttGroup",
 *         type="integer",
 *         description="Unique identifier for the attribute group"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the attribute group"
 *     ),
 *     @OA\Property(
 *         property="enabled",
 *         type="boolean",
 *         description="Indicates whether the attribute group is enabled"
 *     ),
 *     @OA\Property(
 *         property="ordering",
 *         type="integer",
 *         description="Ordering position of the attribute group"
 *     )
 * )
 */
class AttributeGroup extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rangeAttributesGroups';

    /**
     * Indicates whether the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idAttGroup';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'enabled',
        'ordering',
    ];
}
