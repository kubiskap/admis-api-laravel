<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Communication
 *
 * Represents a communication entity in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Communication",
 *     description="Communication model",
 *     @OA\Property(
 *         property="idCommunication",
 *         type="integer",
 *         description="Unique identifier for the communication"
 *     ),
 *     @OA\Property(
 *         property="idCommunicationType",
 *         type="integer",
 *         description="Identifier of the related communication type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the communication"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the communication is hidden"
 *     )
 * )
 */
class Communication extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeCommunications';

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
    protected $primaryKey = 'idCommunication';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idCommunicationType',
        'name',
        'hidden',
    ];
}
