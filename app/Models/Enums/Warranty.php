<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Warranty
 *
 * Represents a warranty in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Warranty",
 *     description="Warranty model",
 *     @OA\Property(
 *         property="idWarranty",
 *         type="integer",
 *         description="Unique identifier for the warranty"
 *     ),
 *     @OA\Property(
 *         property="period",
 *         type="integer",
 *         description="Warranty period in months"
 *     ),
 *     @OA\Property(
 *         property="idWarrantyType",
 *         type="integer",
 *         description="Identifier of the associated warranty type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the warranty is hidden"
 *     )
 * )
 */
class Warranty extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeWarranties';

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
    protected $primaryKey = 'idWarranty';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'period',
        'idWarrantyType',
        'hidden',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'period' => 'integer',
        'hidden' => 'boolean',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Many warranties belong to one warranty type (rangeWarrantyTypes).
     * warranty.idWarrantyType -> rangeWarrantyTypes.idWarrantyType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function warrantyType(): BelongsTo
    {
        return $this->belongsTo(WarrantyType::class, 'idWarrantyType', 'idWarrantyType');
    }

}
