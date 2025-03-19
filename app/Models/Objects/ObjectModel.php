<?php

namespace App\Models\Objects;

use Illuminate\Database\Eloquent\Model;

use App\Models\Enums\ObjectType;
use App\Models\Project\Project;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ObjectModel extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'objects';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'idObject';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'idProject',
        'idObjectType',
        'name',
    ];

    /**
     * Get the project that owns the object.
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Project\Project::class, 'idProject', 'idProject');
    }

    /**
     * Get the object type as an enum.
     */
    public function objectType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ObjectType::class, 'idObjectType', 'idObjectType');
    }
}