<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ContactType
 *
 * Represents a type of contact in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="ContactType",
 *     description="ContactType model",
 *     @OA\Property(
 *         property="idContactType",
 *         type="integer",
 *         description="Unique identifier for the contact type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the contact type"
 *     ),
 *     @OA\Property(
 *         property="nameEn",
 *         type="string",
 *         description="English name of the contact type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the contact type is hidden"
 *     )
 * )
 */
class ContactType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeContactTypes';

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
    protected $primaryKey = 'idContactType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nameEn',
        'hidden',
    ];
}