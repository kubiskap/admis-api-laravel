<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectCompany extends Pivot
{
    /**
     * The table associated with the pivot model.
     */
    protected $table = 'project2company';

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
        'idCompany',
        'idCompanyType',
    ];

    /**
     * Relationship to the Enums\ContactType model
     * project2contact.idContactType -> contactTypes.idContactType
     */
    public function companyType(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Enums\CompanyType::class, 'idCompanyType', 'idCompanyType');
    }
}
