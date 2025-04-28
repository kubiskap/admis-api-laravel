<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProjectContact
 *
 * Represents the pivot table linking projects and contacts.
 *
 * @package App\Models\Pivots
 *
 * @OA\Schema(
 *     schema="ProjectContact",
 *     description="ProjectContact model",
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="idContact",
 *         type="integer",
 *         description="Identifier for the contact"
 *     ),
 *     @OA\Property(
 *         property="idContactType",
 *         type="integer",
 *         description="Identifier for the contact type"
 *     )
 * )
 */
class ProjectContact extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'project2contact';

    /**
     * Indicates if the pivot has an auto-incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Define the primary key for the pivot model if needed.
     *
     * @var null
     */
    protected $primaryKey = null;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idProject',
        'idContact',
        'idContactType',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Relationship to the ContactType model.
     * project2contact.idContactType -> contactTypes.idContactType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function contactType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ContactType::class, 'idContactType', 'idContactType');
    }

}
