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

/**
 * A work item
 *
 * @property int                  $id          The internal work item id
 * @property int                  $project_id  The work item's project id
 * @property BelongsTo<Project>   $project     The project the work item belongs to
 * @property int                  $owner_id    The work item's owner
 * @property BelongsTo<User>      $owner       The work item owner
 * @property string               $name        The work item's name
 * @property boolean              $is_open     Is the work item open?
 * @property Carbon               $start_date  The start date of the work item
 * @property ?Carbon              $end_date    The end date of the work item, optional
 * @property array                $ticket_data Ticket data. Used to tie the work time to an external ticket system
 * @property HasManyThrough<User> $users       All users assigned to this work item
 * @property HasMany<TimeEntry>   time_entries  Time entries for this work item
 * @property Carbon               $created_at
 * @property Carbon               $updated_at
 */
class WorkItem extends Model
{
    use HasFactory;

    public const TABLE       = 'work_items';
    public const ID          = 'id';
    public const PROJECT_ID  = 'project_id';
    public const OWNER_ID    = 'owner_id';
    public const NAME        = 'name';
    public const IS_OPEN     = 'is_open';
    public const START_DATE  = 'start_date';
    public const END_DATE    = 'end_date';
    public const TICKET_DATA = 'ticket_data';
    public const CREATED_AT  = 'created_at';
    public const UPDATED_AT  = 'updated_at';

    protected $table = self::TABLE;

    /**
     * Fields that may be modified en masses
     *
     * @var array<string>
     */
    protected $fillable = [
        self::PROJECT_ID,
        self::OWNER_ID,
        self::NAME,
        self::IS_OPEN,
        self::START_DATE,
        self::END_DATE,
        self::TICKET_DATA
    ];

    /**
     * Fields to automatically convert
     *
     * @var array<string, string>
     */
    protected $casts = [
        self::START_DATE  => 'datetime',
        self::END_DATE    => 'datetime',
        self::TICKET_DATA => 'array'
    ];

    /**
     * Field default values
     *
     * @var array<string, string>
     */
    protected $attributes = [
        self::TICKET_DATA => '[]'
    ];

    /**
     * Filter work items by open state
     *
     * @param Builder $query
     * @param bool    $isOpen
     *
     * @return void
     */
    public function scopeWhereOpen(Builder $query, bool $isOpen = true): void
    {
        $query->where(self::IS_OPEN, $isOpen);
    }

    /**
     * The project this work item belongs to
     *
     * @return BelongsTo<Project>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(
            Project::class,
            self::PROJECT_ID,
            Project::ID
        );
    }

    /**
     * Filter work items by project id
     *
     * @param Builder $query
     * @param int     $projectId
     *
     * @return void
     */
    public function scopeWhereProject(Builder $query, int $projectId): void
    {
        $query->where(self::PROJECT_ID, $projectId);
    }

    /**
     * The owner of this work item
     *
     * @return BelongsTo<User>
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::OWNER_ID,
            User::ID
        );
    }

    /**
     * Filter work items by owner id
     *
     * @param Builder $query
     * @param int     $ownerId
     *
     * @return void
     */
    public function scopeWhereOwner(Builder $query, int $ownerId): void
    {
        $query->where(self::OWNER_ID, $ownerId);
    }

    /**
     * The assigned work item users
     *
     * @return BelongstoMany<User>
     */
    public function users(): BelongstoMany
    {
        return $this->BelongstoMany(
            User::class,
            WorkItemUser::class,
            WorkItemUser::WORK_ITEM_ID,
            WorkItemUser::USER_ID,
            self::ID,
            User::ID
        );
    }

    /**
     * All the time entries for this work item
     *
     * @return HasMany<TimeEntry>
     */
    public function time_entries(): HasMany
    {
        return $this->hasMany(
            TimeEntry::class,
            TimeEntry::WORK_ITEM_ID,
            self::ID
        );
    }
}
