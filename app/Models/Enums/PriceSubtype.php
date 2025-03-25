<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

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
        'hidden'
    ];

    public function priceType()
    {
        return $this->belongsTo(PriceType::class, 'idPriceSubtype', 'idPriceSubtypes');
    }
}