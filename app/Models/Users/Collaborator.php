<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Model;

class Collaborator extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'collaborator';

    /**
     * The primary key for the table.
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the primary key is auto-incrementing.
     */
    public $incrementing = true; // 'id' is auto-incrementing per your schema

    /**
     * Indicates if the model should be timestamped using created_at/updated_at columns.
     * collaborator has a 'created' datetime, so we set this to false to avoid confusion.
     */
    public $timestamps = false;

    /**
     * The attributes that can be mass assigned.
     */
    protected $fillable = [
        'username',
        'collaborator',
        'created',
        'begin',
        'expiry',
        'active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'created' => 'datetime',
        'begin' => 'datetime',
        'expiry' => 'datetime',
        'active' => 'boolean',
    ];

    /************************************************
     *                   RELATIONSHIPS
     ************************************************/
    
    /**
     * collaborator.username -> users.username
     * This is the user who owns or created the collaborator relationship.
     */
    public function user()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'username', 'username');
    }

    /**
     * collaborator.collaborator -> users.username
     * This is the "other" user assigned as collaborator.
     */
    public function collaboratorUser()
    {
        return $this->belongsTo(\App\Models\Users\User::class, 'collaborator', 'username');
    }

    /************************************************
     *               CUSTOM METHODS
     ************************************************/

    /**
     * Example scope to find only currently active collaborators.
     * 'active' is a boolean set to 1 or 0.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Mark this collaborator relationship as inactive (soft approach).
     */
    public function deactivate()
    {
        $this->active = false;
        $this->save();
    }
}
