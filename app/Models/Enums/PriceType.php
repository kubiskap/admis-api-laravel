<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

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
        'ordering'
    ];

    public function priceSubtype()
    {
        return $this->hasOne(PriceSubtype::class, 'idPriceSubtypes', 'idPriceSubtype');
    }
}
