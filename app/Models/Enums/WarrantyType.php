<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class WarrantyType
 *
 * Represents a type of warranty in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="WarrantyType",
 *     description="WarrantyType model",
 *     @OA\Property(
 *         property="idWarrantyType",
 *         type="integer",
 *         description="Unique identifier for the warranty type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the warranty type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the warranty type is hidden"
 *     ),
 *     @OA\Property(
 *         property="nameForPOST",
 *         type="string",
 *         description="Name used for POST requests"
 *     )
 * )
 */
class WarrantyType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeWarrantiesTypes';

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
    protected $primaryKey = 'idWarrantyType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden',
        'nameForPOST',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'hidden' => 'boolean',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * One warranty type is associated with many warranties (rangeWarranties).
     * rangeWarranties.idWarrantyType -> rangeWarrantiesTypes.idWarrantyType
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function warranties(): HasMany
    {
        return $this->hasMany(Warranty::class, 'idWarrantyType', 'idWarrantyType');
    }

}
