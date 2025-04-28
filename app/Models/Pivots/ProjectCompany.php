<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class ProjectCompany
 *
 * Represents the pivot table linking projects and companies.
 *
 * @package App\Models\Pivots
 *
 * @OA\Schema(
 *     schema="ProjectCompany",
 *     description="ProjectCompany model",
 *     @OA\Property(
 *         property="idProject",
 *         type="integer",
 *         description="Identifier for the project"
 *     ),
 *     @OA\Property(
 *         property="idCompany",
 *         type="integer",
 *         description="Identifier for the company"
 *     ),
 *     @OA\Property(
 *         property="idCompanyType",
 *         type="integer",
 *         description="Identifier for the company type"
 *     )
 * )
 */
class ProjectCompany extends Pivot
{
    /**
     * The table associated with the pivot model.
     *
     * @var string
     */
    protected $table = 'project2company';

    /**
     * Indicates if the pivot has an auto-incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the pivot model.
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
        'idCompany',
        'idCompanyType',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * Relationship to the CompanyType model.
     * project2company.idCompanyType -> companyTypes.idCompanyType
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function companyType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\CompanyType::class, 'idCompanyType', 'idCompanyType');
    }
}
