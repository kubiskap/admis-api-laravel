<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Price extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'prices';

    /**
     * Indicates if the model should be timestamped (created_at, updated_at).
     * The 'prices' table has no such columns, so set false.
     */
    public $timestamps = false;

    /**
     * The table has a composite key (idPriceType, idProject).
     * Eloquent does not natively support composite primary keys,
     * so we disable incrementing and, if desired, set $primaryKey to null.
     */
    protected $primaryKey = null;  // or 'idProject' if you want, but less accurate for a composite
    public $incrementing = false;

    /**
     * The attributes that can be mass assigned.
     */
    protected $fillable = [
        'idPriceType',
        'idProject',
        'value',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'value' => 'float',
    ];

    /**
     * If you want to link each price row to its project (projects.idProject),
     * define a belongsTo relationship:
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * If you have a table for price types (rangePriceTypes) in your Enums folder,
     * define a relationship similarly:
     */
    public function priceType(): BelongsTo
    {
        // Adjust the namespace/model name if your "rangePriceTypes" model is different
        return $this->belongsTo(\App\Models\Enums\PriceType::class, 'idPriceType', 'idPriceType');
    }
}
