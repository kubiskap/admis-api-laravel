<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

use MatanYadaev\EloquentSpatial\Objects\LineString;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;
use MatanYadaev\EloquentSpatial\SpatialBuilder;

class ProjectCommunication extends Pivot
{

    use HasSpatial;

    /**
     * The table associated with the pivot model.
     */
    protected $table = 'project2communication';

    /**
     * Indicates that this pivot table does not have
     * an auto-incrementing primary key.
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
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
     */
    protected $casts = [
        'allPointsWgs' => LineString::class,
        'geometryWgs' => LineString::class,
        'allPointsSjtsk' => LineString::class,
        'geometrySjtsk' => LineString::class,
        'gpsN1' => 'float',
        'gpsN2' => 'float',
        'gpsE1' => 'float',
        'gpsE2' => 'float',
        'stationingFrom' => 'float',
        'stationingTo' => 'float',
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * Link to the Project model:
     * project2communication.idProject -> projects.idProject
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Link to the Communication model:
     * project2communication.idCommunication -> rangeCommunications.idCommunication
     */
    public function communication(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\Communication::class, 'idCommunication', 'idCommunication');
    }
}
