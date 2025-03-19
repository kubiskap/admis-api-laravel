<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class Ou extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ou';

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
    protected $primaryKey = 'idOu';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    ];
}
