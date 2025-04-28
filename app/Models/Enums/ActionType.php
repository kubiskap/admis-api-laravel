<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ActionType
 *
 * Represents the types of actions available in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="ActionType",
 *     description="ActionType model",
 *     @OA\Property(
 *         property="idActionType",
 *         type="integer",
 *         description="Unique identifier for the action type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the action type"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Detailed description of the action type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the action type is hidden"
 *     )
 * )
 */
class ActionType extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rangeActionTypes';

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
    protected $primaryKey = 'idActionType';

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
