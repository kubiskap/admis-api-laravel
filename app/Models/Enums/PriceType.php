<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PriceType
 *
 * Represents a type of price in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="PriceType",
 *     description="PriceType model",
 *     @OA\Property(
 *         property="idPriceType",
 *         type="integer",
 *         description="Unique identifier for the price type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the price type"
 *     ),
 *     @OA\Property(
 *         property="nameEn",
 *         type="string",
 *         description="English name of the price type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the price type is hidden"
 *     ),
 *     @OA\Property(
 *         property="idPriceSubtype",
 *         type="integer",
 *         description="Identifier of the related price subtype"
 *     ),
 *     @OA\Property(
 *         property="ordering",
 *         type="integer",
 *         description="Ordering position of the price type"
 *     )
 * )
 */
class PriceType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangePriceTypes';

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
    protected $primaryKey = 'idPriceType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nameEn',
        'hidden',
        'idPriceSubtype',
        'ordering',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * One price type has one price subtype (rangePriceSubtype).
     * priceType.idPriceSubtype -> rangePriceSubtype.idPriceSubtypes
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function priceSubtype()
    {
        return $this->hasOne(PriceSubtype::class, 'idPriceSubtypes', 'idPriceSubtype');
    }
}
