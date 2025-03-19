<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class ReportConfig extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'reportConfig';

    /**
     * The primary key on this table.
     */
    protected $primaryKey = 'idReportConfig';

    /**
     * Indicates if the model should be timestamped.
     * (Set to false unless you have 'created_at'/'updated_at' columns in this table.)
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'note',
        'ouIds',
        'usernames',
        'reportType', // enum('editor','manager','noreport','dummy')
    ];

    /************************************************
     *               RELATIONSHIPS
     ************************************************/

    /**
     * One report config can be related to many users (users.idReportConfig).
     */
    public function users()
    {
        return $this->hasMany(\App\Models\Users\User::class, 'idReportConfig', 'idReportConfig');
    }
}