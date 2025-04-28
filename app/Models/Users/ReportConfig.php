<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ReportConfig
 *
 * Represents the configuration for generating user reports.
 *
 * @package App\Models\Users
 *
 * @OA\Schema(
 *     schema="ReportConfig",
 *     description="Configuration model for user reports",
 *     @OA\Property(
 *         property="idReportConfig",
 *         type="integer",
 *         description="Unique identifier for the report configuration"
 *     ),
 *     @OA\Property(
 *         property="note",
 *         type="string",
 *         description="Note or description for the report configuration"
 *     ),
 *     @OA\Property(
 *         property="ouIds",
 *         type="string",
 *         description="Comma-separated list of organizational unit IDs associated with the report"
 *     ),
 *     @OA\Property(
 *         property="usernames",
 *         type="string",
 *         description="Comma-separated list of usernames to which the report configuration applies"
 *     ),
 *     @OA\Property(
 *         property="reportType",
 *         type="string",
 *         enum={"editor", "manager", "noreport", "dummy"},
 *         description="Type of report configuration"
 *     )
 * )
 */
class ReportConfig extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'reportConfig';

    /**
     * The primary key on this table.
     *
     * @var string
     */
    protected $primaryKey = 'idReportConfig';

    /**
     * Indicates if the model should be timestamped.
     * (Set to false unless you have 'created_at'/'updated_at' columns in this table.)
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
        'note',
        'ouIds',
        'usernames',
        'reportType',
    ];

    /************************************************
     *             RELATIONSHIPS
     ************************************************/

    /**
     * One report config can be related to many users (users.idReportConfig).
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(\App\Models\Users\User::class, 'idReportConfig', 'idReportConfig');
    }
}