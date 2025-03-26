<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectTypeProjectSubtype extends Pivot
{
    /**
     * The table associated with the pivot model.
     */
    protected $table = 'type2subtype';

    /**
     * Indicates that this pivot table does not have an auto-incrementing primary key.
     * The PK is a composite: (idProjectType, idProjectSubtype).
     */
    public $incrementing = false;

    /**
     * Disable timestamps (created_at, updated_at) for this pivot.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned, if needed.
     */
    protected $fillable = [
        'idProjectType',
        'idProjectSubtype',
        'idPriorityConfig',
    ];

    /**
     * If you want to cast certain fields, you can do so. For instance,
     * 'idProjectType' => 'integer', etc. 
     */
    protected $casts = [
        'idProjectType'    => 'integer',
        'idProjectSubtype' => 'integer',
        'idPriorityConfig' => 'integer',
    ];

    /**
     * Relationship to the rangePriorityScaleConfig table:
     * type2subtype.idPriorityConfig -> rangePriorityScaleConfig.idPriorityConfig
     */
    public function priorityConfig(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Enums\PriorityScaleConfig::class,
            'idPriorityConfig',
            'idPriorityConfig'
        );
    }
}