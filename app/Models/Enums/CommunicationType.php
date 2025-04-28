<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CommunicationType
 *
 * Represents a type of communication in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="CommunicationType",
 *     description="CommunicationType model",
 *     @OA\Property(
 *         property="idCommunicationType",
 *         type="integer",
 *         description="Unique identifier for the communication type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the communication type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the communication type is hidden"
 *     )
 * )
 */
class CommunicationType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeCommunicationTypes';

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
    protected $primaryKey = 'idCommunicationType';

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
