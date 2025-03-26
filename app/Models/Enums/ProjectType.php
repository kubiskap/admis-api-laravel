<?php

namespace App\Models\Enums;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeProjectTypes';

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
    protected $primaryKey = 'idProjectType';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden'
    ];

    /**
     * Many-to-Many relationship to ProjectSubtype via type2subtype table.
     */
    public function subtypes(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\ProjectSubtype::class,
            'type2subtype',
            'idProjectType',
            'idProjectSubtype',
        )
        ->using(\App\Models\Pivots\ProjectTypeProjectSubtype::class)
        ->withPivot('idPriorityConfig'); 
    }
}
