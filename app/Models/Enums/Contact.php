<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Contact
 *
 * Represents a contact entity in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Contact",
 *     description="Contact model",
 *     @OA\Property(
 *         property="idContact",
 *         type="integer",
 *         description="Unique identifier for the contact"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the contact"
 *     ),
 *     @OA\Property(
 *         property="phone",
 *         type="string",
 *         description="Phone number of the contact"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="Email address of the contact"
 *     ),
 *     @OA\Property(
 *         property="active",
 *         type="boolean",
 *         description="Indicates whether the contact is active"
 *     ),
 *     @OA\Property(
 *         property="updated",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp of the last update"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="string",
 *         description="Identifier of the user who last updated the contact"
 *     )
 * )
 */
class Contact extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeContacts';

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
    protected $primaryKey = 'idContact';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'active',
        'updated',
        'updated_by',
    ];
}
