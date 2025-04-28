<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

/**
 * Class ProjectCommunication
 *
 * Represents the pivot table linking projects and communications.
 *
 * @package App\Models\Pivots
 *
 * @OA\Schema(
 *     schema="ProjectCommunication",
 *     description="ProjectCommunication model",
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="idCommunication",
 *         type="integer",
 *         description="Identifier for the communication"
 *     ),
 *     @OA\Property(
 *         property="stationingFrom",
 *         type="number",
 *         format="float",
 *         description="Starting stationing value"
 *     ),
 *     @OA\Property(
 *         property="stationingTo",
 *         type="number",
 *         format="float",
 *         description="Ending stationing value"
 *     ),
 *     @OA\Property(
 *         property="gpsN1",
 *         type="number",
 *         format="float",
 *         description="GPS coordinate N1"
 *     ),
 *     @OA\Property(
 *         property="gpsN2",
 *         type="number",
 *         format="float",
 *         description="GPS coordinate N2"
 *     ),
 *     @OA\Property(
 *         property="gpsE1",
 *         type="number",
 *         format="float",
 *         description="GPS coordinate E1"
 *     ),
 *     @OA\Property(
 *         property="gpsE2",
 *         type="number",
 *         format="float",
 *         description="GPS coordinate E2"
 *     ),
 *     @OA\Property(
 *         property="allPointsWgs",
 *         type="string",
 *         description="All points in WGS coordinate system (serialized)"
 *     ),
 *     @OA\Property(
 *         property="allPointsSjtsk",
 *         type="string",
 *         description="All points in SJTSK coordinate system (serialized)"
 *     ),
 *     @OA\Property(
 *         property="geometryWgs",
 *         type="string",
 *         description="Geometry in WGS coordinate system (serialized)"
 *     ),
 *     @OA\Property(
 *         property="geometrySjtsk",
 *         type="string",
 *         description="Geometry in SJTSK coordinate system (serialized)"
 *     ),
 *     @OA\Property(
 *         property="comment",
 *         type="string",
 *         description="Additional comment"
 *     )
 * )
 */
class ProjectCommunication extends Pivot
{
    use HasSpatial;

    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'project2communication';

    /**
     * Indicates that this pivot table does not have
     * an auto-incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'idProject',
        'idCommunication',
        'stationingFrom',
        'stationingTo',
        'gpsN1',
        'gpsN2',
        'gpsE1',
        'gpsE2',
        'allPointsWgs',
        'allPointsSjtsk',
        'geometryWgs',
        'geometrySjtsk',
        'comment',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'allPointsWgs'     => LineString::class,
        'geometryWgs'      => LineString::class,
        'allPointsSjtsk'   => LineString::class,
        'geometrySjtsk'    => LineString::class,
        'gpsN1'            => 'float',
        'gpsN2'            => 'float',
        'gpsE1'            => 'float',
        'gpsE2'            => 'float',
        'stationingFrom'   => 'float',
        'stationingTo'     => 'float',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Link to the Project model:
     * project2communication.idProject -> projects.idProject
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Link to the Communication model:
     * project2communication.idCommunication -> rangeCommunications.idCommunication
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Communication::class, 'idCommunication', 'idCommunication');
    }
}
