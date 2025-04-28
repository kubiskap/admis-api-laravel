<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Ou
 *
 * Represents an organizational unit (OU) in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Ou",
 *     description="Ou model",
 *     @OA\Property(
 *         property="idOu",
 *         type="integer",
 *         description="Unique identifier for the organizational unit"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the organizational unit"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the organizational unit is hidden"
 *     )
 * )
 */
class Ou extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ou';

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
    protected $primaryKey = 'idOu';

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
