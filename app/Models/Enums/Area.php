<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Area
 *
 * Represents different areas available in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Area",
 *     description="Area model",
 *     @OA\Property(
 *         property="idArea",
 *         type="integer",
 *         description="Unique identifier for the area"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the area"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the area is hidden"
 *     )
 * )
 */
class Area extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rangeAreas';

    /**
     * Indicates whether the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idArea';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden',
    ];
}
