<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\MultiLineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

/**
 * Class ExternalApi
 *
 * Represents an external API view for project data.
 *
 * @package App\Models\Views
 *
 * @OA\Schema(
 *     schema="ExternalApi",
 *     description="View model for project external API data",
 *     @OA\Property(
 *         property="id_projektu",
 *         type="integer",
 *         description="Unique identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="cena",
 *         type="number",
 *         format="float",
 *         description="Price associated with the project"
 *     ),
 *     @OA\Property(
 *         property="komunikace_array",
 *         type="object",
 *         description="Serialized JSON array of communications"
 *     ),
 *     @OA\Property(
 *         property="priorita_skore",
 *         type="number",
 *         format="float",
 *         description="Priority score for the project"
 *     ),
 *     @OA\Property(
 *         property="priorita_korekce",
 *         type="number",
 *         format="float",
 *         description="Priority correction factor"
 *     ),
 *     @OA\Property(
 *         property="datum_posledni_zmeny",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp of the last update"
 *     ),
 *     @OA\Property(
 *         property="predani_staveniste",
 *         type="string",
 *         format="date-time",
 *         description="Datetime when handing over the construction site"
 *     ),
 *     @OA\Property(
 *         property="dokonceni_stavby",
 *         type="string",
 *         format="date-time",
 *         description="Datetime when the construction was completed"
 *     ),
 *     @OA\Property(
 *         property="zaruka_technologicka",
 *         type="string",
 *         format="date-time",
 *         description="Datetime for the technological warranty period"
 *     ),
 *     @OA\Property(
 *         property="zaruka_stavebni",
 *         type="string",
 *         format="date-time",
 *         description="Datetime for the construction warranty period"
 *     )
 * )
 */
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

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
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
