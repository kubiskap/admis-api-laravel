<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ActionsLogAll extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'viewActionsLogAll';

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
    protected $primaryKey = 'idAction';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;
}
