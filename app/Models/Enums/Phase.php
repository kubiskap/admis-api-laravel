<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class Phase extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangePhases';

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
    protected $primaryKey = 'idPhase';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'nameEn',
        'level',
        'phasing',
        'phaseColor',
        'phaseColorClass',
        'hidden',
        'phaseForLiteProject'
    ];
}
