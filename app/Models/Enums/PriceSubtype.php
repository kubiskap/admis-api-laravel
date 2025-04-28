<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceSubtype
 *
 * Represents a subtype of price in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="PriceSubtype",
 *     description="PriceSubtype model",
 *     @OA\Property(
 *         property="idPriceSubtypes",
 *         type="integer",
 *         description="Unique identifier for the price subtype"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the price subtype"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the price subtype is hidden"
 *     )
 * )
 */
class PriceSubtype extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangePriceSubtype';

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
    protected $primaryKey = 'idPriceSubtypes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Many price subtypes belong to one PriceType (rangePriceTypes).
     * priceSubtype.idPriceSubtype -> rangePriceTypes.idPriceSubtypes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priceType()
    {
        return $this->belongsTo(PriceType::class, 'idPriceSubtype', 'idPriceSubtypes');
    }
}