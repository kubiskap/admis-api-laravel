<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectContact extends Pivot
{
    /**
     * The table associated with the pivot model.
     */
    protected $table = 'project2contact';

    /**
     * Indicates if the pivot has auto-incrementing primary key.
     * In your schema, the pivot doesn't have an autoincrement PK,
     * so set this to false if there's no single integer PK.
     */
    public $incrementing = false;

    /**
     * If needed, define the primary keys for the table for Eloquent if you want:
     */
    protected $primaryKey = null; // or define composite if needed
    public $timestamps = false;

    /**
     * Accessors for relationships or custom columns...
     */
    protected $fillable = [
        'idProject',
        'idContact',
        'idContactType',
    ];

    /**
     * Relationship to the Enums\ContactType model
     * project2contact.idContactType -> contactTypes.idContactType
     */
    public function contactType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\ContactType::class, 'idContactType', 'idContactType');
    }
}
