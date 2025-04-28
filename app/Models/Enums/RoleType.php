<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RoleType
 *
 * Represents a type of role in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="RoleType",
 *     description="RoleType model",
 *     @OA\Property(
 *         property="idRoleType",
 *         type="integer",
 *         description="Unique identifier for the role type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the role type"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Description of the role type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the role type is hidden"
 *     )
 * )
 */
class RoleType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeRoleTypes';

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
    protected $primaryKey = 'idRoleType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'hidden',
    ];
}