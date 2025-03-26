<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaskStatus extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'rangeTaskStatuses';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'idTaskStatus';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Adjust if your schema suggests otherwise (e.g. no AUTO_INCREMENT).
     */
    public $incrementing = true;

    /**
     * This table likely does not have created_at/updated_at fields.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     */
    protected $fillable = [
        'name',
        'isTerminal',
        'isEnabled',
        'rank',
        'statusColor',
        'statusClass',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'isTerminal' => 'boolean',
        'isEnabled'  => 'boolean',
        'rank'       => 'integer',
    ];

    /**
     * If you want to relate this TaskStatus to all TaskVersions
     * that use it (taskVersions.idTaskStatus -> rangeTaskStatuses.idTaskStatus):
     */
    public function taskVersions(): HasMany
    {
        return $this->hasMany(\App\Models\Tasks\TaskVersion::class, 'idTaskStatus', 'idTaskStatus');
    }
}
