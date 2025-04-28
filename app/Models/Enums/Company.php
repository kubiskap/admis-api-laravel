<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Company
 *
 * Represents a company entity in the system.
 *
 * @package App\Models\Enums
 *
 * @OA\Schema(
 *     schema="Company",
 *     description="Company model",
 *     @OA\Property(
 *         property="idCompany",
 *         type="integer",
 *         description="Unique identifier for the company"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="Name of the company"
 *     ),
 *     @OA\Property(
 *         property="address",
 *         type="string",
 *         description="Address of the company"
 *     ),
 *     @OA\Property(
 *         property="ic",
 *         type="string",
 *         description="Company identification number"
 *     ),
 *     @OA\Property(
 *         property="dic",
 *         type="string",
 *         description="Tax identification number of the company"
 *     ),
 *     @OA\Property(
 *         property="www",
 *         type="string",
 *         description="Website of the company"
 *     )
 * )
 */
class Company extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeCompanies';

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
    protected $primaryKey = 'idCompany';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'ic',
        'dic',
        'www',
    ];
}
