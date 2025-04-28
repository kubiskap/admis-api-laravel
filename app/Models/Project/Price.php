<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Price
 *
 * Represents the price information for a project.
 *
 * @package App\Models\Project
 *
 * @OA\Schema(
 *     schema="Price",
 *     description="Price model",
 *     @OA\Property(
 *         property="idPriceType",
 *         type="integer",
 *         description="Identifier for the price type"
 *     ),
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="value",
 *         type="number",
 *         format="float",
 *         description="Price value"
 *     )
 * )
 */
class Price extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'prices';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the table.
     *
     * Note: This table uses a composite key, so auto-incrementing is disabled.
     *
     * @var null
     */
    protected $primaryKey = null;
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idPriceType',
        'idProject',
        'value',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'float',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Get the project associated with this price.
     * prices.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Get the price type associated with this price.
     * prices.idPriceType -> rangePriceTypes.idPriceType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priceType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\PriceType::class, 'idPriceType', 'idPriceType');
    }
}
