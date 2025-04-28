<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class AuthorityType
 *
 * Represents a type of authority in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="AuthorityType",
 *     description="AuthorityType model",
 *     @OA\Property(
 *         property="idAuthorityType",
 *         type="integer",
 *         description="Unique identifier for the authority type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the authority type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the authority type is hidden"
 *     )
 * )
 */
class AuthorityType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeAuthoritiyTypes';

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
    protected $primaryKey = 'idAuthorityType';

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
