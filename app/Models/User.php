<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * User
 *
 * @property int                 $id                         User id
 * @property string              $first_name                 User's first name
 * @property string              $last_name                  User's last name
 * @property string              $email                      User's email
 * @property ?Carbon             $email_verified_at          When the user's email was verified, optional.
 * @property string              $password                   User's password
 * @property ?string             $two_factor_secret          User's two factor secret
 * @property ?string             $two_factor_recovery_codes  User's two factor recovery codes
 * @property ?string             $two_factor_confirmed_at    Timestamp when the user confirmed their two factor
 * @property string              $remember_token             The user's remember me token
 * @property ?int                $current_team_id            The user's current team
 * @property ?string             $profile_photo_path         The user's profile photo path
 * @property Carbon              $created_at
 * @property Carbon              $updated_at
 * @property HasMany<Project>    $customer_projects
 * @property HasMany<Project>    $project_manager_projects
 * @property BelongsToMany<Role> $roles
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    public const TABLE                     = 'users';
    public const ID                        = 'id';
    public const FIRST_NAME                = 'first_name';
    public const LAST_NAME                 = 'last_name';
    public const EMAIL                     = 'email';
    public const EMAIL_VERIFIED_AT         = 'email_verified_at';
    public const PASSWORD                  = 'password';
    public const TWO_FACTOR_SECRET         = 'two_factor_secret';
    public const TWO_FACTOR_RECOVERY_CODES = 'two_factor_recovery_codes';
    public const TWO_FACTOR_CONFIRMED_AT   = 'two_factor_confirmed_at';
    public const REMEMBER_TOKEN            = 'remember_token';
    public const CURRENT_TEAM_ID           = 'current_team_id';
    public const PROFILE_PHOTO_PATH        = 'profile_photo_path';
    public const CREATED_AT                = 'created_at';
    public const UPDATED_AT                = 'updated_at';

    protected $table = self::TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        self::FIRST_NAME,
        self::LAST_NAME,
        self::EMAIL,
        self::PASSWORD,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        self::PASSWORD,
        self::REMEMBER_TOKEN,
        self::TWO_FACTOR_RECOVERY_CODES,
        self::TWO_FACTOR_SECRET,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::EMAIL_VERIFIED_AT => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * All projects this user is a customer on
     *
     * @return HasMany<Project>
     */
    public function customer_projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            Project::CUSTOMER_ID,
            self::ID
        );
    }

    /**
     * All the projects this user is a project manager on
     *
     * @return HasMany<Project>
     */
    public function project_manager_projects(): HasMany
    {
        return $this->hasMany(
            Project::class,
            Project::PROJECT_MANAGER_ID,
            self::ID
        );
    }

    /**
     * All the projects a user is assigned to as a project user
     *
     * @return HasManyThrough<Project>
     */
    public function projects(): HasManyThrough
    {
        return $this->hasManyThrough(
            Project::class,
            ProjectUser::class,
            ProjectUser::USER_ID,
            Project::ID,
            self::ID,
            ProjectUser::PROJECT_ID
        );
    }

    /**
     * All the work items owned by this user
     *
     * @return HasMany<WorkItem>
     */
    public function owned_work_items(): HasMany
    {
        return $this->hasMany(
            WorkItem::class,
            WorkItem::OWNER_ID,
            self::ID
        );
    }

    /**
     * All the work items assigned to this user
     *
     * @return HasManyThrough<WorkItem>
     */
    public function assigned_work_items(): HasManyThrough
    {
        return $this->hasManyThrough(
            WorkItem::class,
            WorkItemUser::class,
            WorkItemUser::USER_ID,
            WorkItem::ID,
            self::ID,
            WorkItemUser::WORK_ITEM_ID
        );
    }

    /**
     * Get the user's roles and permissions as a JSON object
     *
     * @return string
     */
    public function getPermissionsAsJson(): string
    {
        return json_encode(
            [
                'roles'       => $this->getRoleNames(),
                'permissions' => $this->getAllPermissions()->pluck('name'),
            ]
        );
    }
}
