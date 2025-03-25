<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

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
        'hidden'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'period' => 'integer',
        'hidden' => 'boolean',
    ];

    public function warrantyType()
    {
        return $this->belongsTo(WarrantyType::class, 'idWarrantyType', 'idWarrantyType');
    }
}
