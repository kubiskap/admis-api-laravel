<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\MultiLineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class ExternalApi extends Model
{
    use HasSpatial;
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

    protected $guarded = [];

    protected $casts = [
        'cena' => 'float',
        'id_projektu' => 'int',
        'komunikace_array' => 'json',
        'priorita_skore' => 'float',
        'priorita_korekce' => 'float',
        'datum_posledni_zmeny' => 'datetime',
        'predani_staveniste' => 'datetime',
        'dokonceni_stavby' => 'datetime',
        'zaruka_technologicka' => 'datetime',
        'zaruka_stavebni' => 'datetime',
    ];
}
