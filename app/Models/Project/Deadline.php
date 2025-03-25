<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deadline extends Model
{
    protected $table = 'deadlines';

    protected $fillable = [
        'idProject',
        'idDeadlineType',
        'value',
        'note',
    ];

    public $timestamps = false;

    public $casts = [
        'value' => 'date',
        'inserted'=> 'date',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    public function deadlineType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\DeadlineType::class, 'idDeadlineType', 'idDeadlineType');
    }
}
