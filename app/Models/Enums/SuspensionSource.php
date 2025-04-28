<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SuspensionSource
 *
 * Represents a source of suspension in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="SuspensionSource",
 *     description="SuspensionSource model",
 *     @OA\Property(
 *         property="idSuspensionSource",
 *         type="integer",
 *         description="Unique identifier for the suspension source"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the suspension source"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the suspension source is hidden"
 *     )
 * )
 */
class SuspensionSource extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeSuspensionSources';

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
    protected $primaryKey = 'idSuspensionSource';

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
