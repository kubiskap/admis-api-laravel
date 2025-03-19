<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class AttributeType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeAttributeTypes';

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
    protected $primaryKey = 'idAttributeType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idAttGroup',
        'name',
        'type',
        'ordering',
        'hidden',
    ];
}
