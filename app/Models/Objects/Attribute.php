<?php

namespace App\Models\Objects;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribute extends Model
{
    /**
     * Tabulka v DB.
     */
    protected $table = 'attributes';

    /**
     * Výchozí Eloquent předpokládá jeden PK; tady máme composite PK.
     * Eloquent nativně composite PK nepodporuje, ale aspoň vypneme incrementing.
     */
    public $incrementing = false;
    public $timestamps = false;

    /**
     * Sloupce, které lze masově přiřadit.
     */
    protected $fillable = [
        'idObject',
        'idAttributeType',
        'value',
    ];

    /**
     * Přetypování hodnot.
     */
    protected $casts = [
        'idObject'         => 'integer',
        'idAttributeType'  => 'integer',
        'value'            => 'string',
    ];

    /**
     * Vztah na objekt (objects.idObject → objects.idObject).
     */
    public function object(): BelongsTo
    {
        return $this->belongsTo(
            ObjectModel::class,
            'idObject',
            'idObject'
        );
    }

    /**
     * Vztah na typ atributu (attributes.idAttributeType → rangeAttributeTypes.idAttributeType).
     */
    public function attributeType(): BelongsTo
    {
        return $this->belongsTo(
            \App\Models\Enums\AttributeType::class,
            'idAttributeType',
            'idAttributeType'
        );
    }
}
