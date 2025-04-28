<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;

/**
 * Class User
 *
 * Represents an application user.
 *
 * @package App\Models\Users
 *
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     required={"username", "name", "email", "idOu", "idRoleType", "idAuthorityType"},
 *     @OA\Property(
 *         property="username",
 *         type="string",
 *         description="Unique identifier for the user (primary key)"
 *     ),
 *     @OA\Property(
 *         property="name",
 *         type="string",
 *         description="The full name of the user"
 *     ),
 *     @OA\Property(
 *         property="email",
 *         type="string",
 *         description="The email address of the user"
 *     ),
 *     @OA\Property(
 *         property="idOu",
 *         type="integer",
 *         description="Identifier for the organizational unit the user belongs to"
 *     ),
 *     @OA\Property(
 *         property="idRoleType",
 *         type="integer",
 *         description="Identifier for the user's role type"
 *     ),
 *     @OA\Property(
 *         property="idAuthorityType",
 *         type="integer",
 *         description="Identifier for the user's authority type"
 *     ),
 *     @OA\Property(
 *         property="accessDenied",
 *         type="boolean",
 *         description="Indicates if the user is currently denied access"
 *     ),
 *     @OA\Property(
 *         property="idReportConfig",
 *         type="integer",
 *         description="Identifier for the associated report configuration"
 *     ),
 *     @OA\Property(
 *         property="editorReport",
 *         type="boolean",
 *         description="Indicates if the user is an editor for reports"
 *     ),
 *     @OA\Property(
 *         property="created",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was created"
 *     ),
 *     @OA\Property(
 *         property="updated",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was last updated"
 *     ),
 *     @OA\Property(
 *         property="deleted",
 *         type="string",
 *         format="date-time",
 *         description="Timestamp when the user was deleted"
 *     )
 * )
 */
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, HasTimestamps;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'username';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The table associated with the model (optional if it matches 'users').
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'created';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'updated';

    /**
     * The name of the "deleted at" column.
     *
     * @var string
     */
    const DELETED_AT = 'deleted';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'idOu',
        'idRoleType',
        'idAuthorityType',
        'accessDenied',
        'idReportConfig',
        'editorReport',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * The attributes that should be hidden when serializing.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'accessDenied' => 'boolean',
        'editorReport' => 'boolean',
        'created' => 'datetime',
        'updated' => 'datetime',
        'deleted' => 'datetime',
    ];

    /************************************************
     *          JWT AUTHENTICATION METHODS
     ************************************************/
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return string|int
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return key-value array of any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [
            'user' => [
                'username'         => $this->username,
                'name'             => $this->name,
                'email'            => $this->email,
                'idOu'             => $this->idOu,
                'idRoleType'       => $this->idRoleType,
                'idAuthorityType'  => $this->idAuthorityType,
                'accessDenied'     => $this->accessDenied,
                'idReportConfig'   => $this->idReportConfig,
                'editorReport'     => $this->editorReport,
            ]
        ];
    }

    /************************************************
     *           RELATIONSHIPS (BELONGS TO)
     ************************************************/

    /**
     * Each user belongs to exactly one OU (organization unit).
     * users.idOu -> ou.idOu
     */
    public function ou()
    {
        return $this->belongsTo(\App\Models\Enums\Ou::class, 'idOu', 'idOu');
    }

    /**
     * Each user has one role type (rangeRoleTypes.idRoleType).
     * users.idRoleType -> rangeRoleTypes.idRoleType
     */
    public function roleType()
    {
        // If you store "rangeRoleTypes" in Models\Enums\RoleType, adjust accordingly
        return $this->belongsTo(\App\Models\Enums\RoleType::class, 'idRoleType', 'idRoleType');
    }

    /**
     * Each user has one authority type (rangeAuthoritiyTypes.idAuthorityType).
     * users.idAuthorityType -> rangeAuthoritiyTypes.idAuthorityType
     */
    public function authorityType()
    {
        // If you store "rangeAuthoritiyTypes" in Models\Enums\AuthorityType, adjust accordingly
        return $this->belongsTo(\App\Models\Enums\AuthorityType::class, 'idAuthorityType', 'idAuthorityType');
    }

    /**
     * Each user can have one associated report configuration (reportConfig.idReportConfig).
     * users.idReportConfig -> reportConfig.idReportConfig
     */
    public function reportConfig()
    {
        return $this->belongsTo(\App\Models\Users\ReportConfig::class, 'idReportConfig', 'idReportConfig');
    }

    /************************************************
     *          RELATIONSHIPS (HAS MANY)
     ************************************************/

    /**
     * One user can be the 'editor' for many projects (projects.editor).
     */
    public function projectsAsEditor()
    {
        return $this->hasMany(\App\Models\Project\Project::class, 'editor', 'username');
    }

    /**
     * One user can be the 'author' for many projects (projects.author).
     */
    public function projectsAsAuthor()
    {
        return $this->hasMany(\App\Models\Project\Project::class, 'author', 'username');
    }

    /**
     * If you also want to track who 'deleted' a project (projects.deleteAuthor),
     * you can define a hasMany here if that column is used for referencing:
     */
    public function projectsAsDeleter()
    {
        return $this->hasMany(\App\Models\Project\Project::class, 'deleteAuthor', 'username');
    }

    /**
     * One user can have many calendar events (calendarEvents.username).
     */
    public function calendarEvents()
    {
        return $this->hasMany(\App\Models\Users\CalendarEvent::class, 'username', 'username');
    }

    /**
     * One user can have many collaborator records referencing them as 'username'.
     * collaborator.username -> users.username
     */
    public function collaboratorRecords()
    {
        return $this->hasMany(\App\Models\Users\Collaborator::class, 'username', 'username');
    }

    /**
     * Another perspective: collaborator.collaborator -> users.username
     * Usually you'd interpret which direction you want to name the relationship.
     * We'll define it if you need to see who "collaborates on me."
     */
    public function collaboratorAssignments()
    {
        return $this->hasMany(\App\Models\Users\Collaborator::class, 'collaborator', 'username');
    }

    /**
     * One user can have many action logs (actionsLogs.username).
     */
    public function actionLogs()
    {
        return $this->hasMany(\App\Models\Logs\ActionLog::class, 'username', 'username');
    }

    /**
     * One user can have many project relations (projectRelations.username).
     */
    public function projectRelations()
    {
        return $this->hasMany(\App\Models\Pivots\ProjectRelation::class, 'username', 'username');
    }

    /**
     * If you treat notifications.username as an actual foreign key referencing users.username,
     * you can define a hasMany for the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(\App\Models\Users\Notification::class, 'username', 'username');
    }

    /**
     * A user can create many suspensions (suspensions.username).
     */
    public function suspensions()
    {
        return $this->hasMany(\App\Models\Project\Suspension::class, 'username', 'username');
    }
}
