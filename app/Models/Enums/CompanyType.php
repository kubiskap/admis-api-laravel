<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CompanyType
 *
 * Represents a type of company in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="CompanyType",
 *     description="CompanyType model",
 *     @OA\Property(
 *         property="idCompanyType",
 *         type="integer",
 *         description="Unique identifier for the company type"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the company type"
 *     ),
 *     @OA\Property(
 *         property="hidden",
 *         type="boolean",
 *         description="Indicates whether the company type is hidden"
 *     )
 * )
 */
class CompanyType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeCompanyTypes';

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
    protected $primaryKey = 'idCompanyType';

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