<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ExternalApi extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'viewProjectAPI';

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
    protected $primaryKey = 'id_projektu';

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
