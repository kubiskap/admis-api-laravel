<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectsPriorityScore extends Model
{
    /**
     * The underlying DB object is a view named "projectsPriorityScore".
     */
    protected $table = 'projectsPriorityScore';

    /**
     * This view likely does not have an auto-increment primary key.
     * If "idProject" is unique for each row, we can treat it as the PK.
     */
    protected $primaryKey = 'idProject';

    /**
     * Since there's no auto-increment or typical PK, disable incrementing.
     */
    public $incrementing = false;

    /**
     * The view does not have created_at/updated_at columns.
     */
    public $timestamps = false;

    /**
     * Mark it as read-only by not allowing mass-assignment (or fill everything).
     * Typically, you don't want to write to a view anyway.
     */
    protected $guarded = [];

    /**
     * Convert numeric fields to floats for easier handling in PHP.
     */
    protected $casts = [
        'priorityScore'   => 'float',
        'maxScore'        => 'float',
        'correctionValue' => 'float',
    ];

    /**
     * Each priority-score record belongs to a Project (if the "view" is keyed by idProject).
     * projectsPriorityScore.idProject -> projects.idProject
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }
}
