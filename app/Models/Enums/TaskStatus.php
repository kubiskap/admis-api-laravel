<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

class TaskStatus extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeTaskStatuses';

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
    protected $primaryKey = 'idTaskStatus';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'isTerminal',
        'isEnabled',
        'rank',
        'statusColor',
        'statusClass'
    ];
}
