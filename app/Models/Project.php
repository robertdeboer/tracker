<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Project
 *
 * @property int                  $id                      Project id
 * @property string               $name                    Project name
 * @property boolean              $is_active               If the project is active
 * @property int                  $customer_id             The project's customer id
 * @property int                  $project_manager_id      The project's project manager id
 * @property ?string              $description             The project's description
 * @property Carbon               $created_at
 * @property Carbon               $updated_at
 * @property HasOne<User>         $customer                The project's customer
 * @property HasOne<User>         $project_manager         The project's project manager
 * @property HasMany<Order>       $orders                  The orders assigned to this project
 * @property HasMany<WorkItem>    $work_items              The project's work items
 * @property HasManyThrough<User> $users                   The project's assigned users
 * @property HasManyThrough<User> $engineers               The project's assigned engineers
 */
class Project extends Model
{
    use HasFactory;

    public const TABLE              = 'projects';
    public const ID                 = 'id';
    public const NAME               = 'name';
    public const IS_ACTIVE          = 'is_active';
    public const CUSTOMER_ID        = 'customer_id';
    public const PROJECT_MANAGER_ID = 'project_manager_id';
    public const DESCRIPTION        = 'description';
    public const CREATED_AT         = 'created_at';
    public const UPDATED_AT         = 'updated_at';

    protected $table = self::TABLE;

    /**
     * Fields that can be modified en masse
     *
     * @var array<string>
     */
    protected $fillable = [
        self::NAME,
        self::IS_ACTIVE,
        self::CUSTOMER_ID,
        self::PROJECT_MANAGER_ID,
        self::DESCRIPTION
    ];

    /**
     * The project's customer
     *
     * @return BelongsTo<User>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::CUSTOMER_ID,
            User::ID
        );
    }

    /**
     * The project's project manager
     *
     * @return BelongsTo<User>
     */
    public function project_manager(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::PROJECT_MANAGER_ID,
            User::ID
        );
    }

    /**
     * The project's orders
     *
     * @return HasMany<Order>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(
            Order::class,
            Order::PROJECT_ID,
            self::ID
        );
    }

    /**
     * The project's work items
     *
     * @return HasMany
     */
    public function work_items(): HasMany
    {
        return $this->hasMany(
            WorkItem::class,
            WorkItem::PROJECT_ID,
            self::ID
        );
    }

    /**
     * Filter project based on is active status
     *
     * @param Builder $query
     * @param bool    $isActive
     *
     * @return void
     */
    public function scopeWhereActive(Builder $query, bool $isActive = true): void
    {
        $query->where(self::IS_ACTIVE, $isActive);
    }

    /**
     * Filter projects by customer id
     *
     * @param Builder $query
     * @param int     $customerId
     *
     * @return void
     */
    public function scopeWhereCustomer(Builder $query, int $customerId): void
    {
        $query->where(self::CUSTOMER_ID, $customerId);
    }

    /**
     * Filter projects by project manager id
     *
     * @param Builder $query
     * @param int     $projectManagerId
     *
     * @return void
     */
    public function scopeWhereProjectManager(Builder $query, int $projectManagerId): void
    {
        $query->where(self::PROJECT_MANAGER_ID, $projectManagerId);
    }

    /**
     * Get all project users
     *
     * Project users are all users that may not be directly assigned to the project
     * but still should have access ot it.
     *
     * @return BelongsToMany<User>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            ProjectUser::TABLE,
        );
    }

    /**
     * The project time entries
     *
     * @return HasManyThrough<TimeEntry>
     */
    public function time_entries(): HasManyThrough
    {
        return $this->hasManyThrough(
            TimeEntry::class,
            WorkItem::class,
            WorkItem::PROJECT_ID,
            TimeEntry::WORK_ITEM_ID,
            self::ID,
            WorkItem::ID
        );
    }
}
