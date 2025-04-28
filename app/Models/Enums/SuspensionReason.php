<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SuspensionReason
 *
 * Represents a reason for suspension in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="SuspensionReason",
 *     description="SuspensionReason model",
 *     @OA\Property(
 *         property="idSuspensionReason",
 *         type="integer",
 *         description="Unique identifier for the suspension reason"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the suspension reason"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the suspension reason is hidden"
 *     )
 * )
 */
class SuspensionReason extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeSuspensionReasons';

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
    protected $primaryKey = 'idSuspensionReason';

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
