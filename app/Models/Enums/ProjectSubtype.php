<?php

namespace App\Models\Enums;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProjectSubtype extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rangeProjectSubtypes';

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
    protected $primaryKey = 'idProjectSubtype';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'hidden'
    ];

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Models\Enums\ProjectType::class,
            'type2subtype',
            'idProjectSubtype',
            'idProjectType'
        )
        ->using(\App\Models\Pivots\ProjectTypeProjectSubtype::class)
        ->withPivot('idPriorityConfig');
    }
}