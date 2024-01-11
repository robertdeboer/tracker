<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

/**
 * A time entry
 *
 * @property int                    $id           The internal time entry id
 * @property int                    $work_item_id The time entry work item id
 * @property BelongsTo<WorkItem>    $work_item     The work item of the time entry
 * @property int                    $author_id    The user who made the time entry
 * @property BelongsTo<User>        $author       The author of the time entry
 * @property float                  $hours        The number of hours entered
 * @property Carbon                 $date         The date and time of the entry
 * @property string                 $note         A note for the entry
 * @property Carbon                 $created_at
 * @property Carbon                 $updated_at
 * @property HasOneThrough<Project> $project      The project this time entry is for
 */
class TimeEntry extends Model
{
    use HasFactory;

    public const TABLE        = 'time_entries';
    public const ID           = 'id';
    public const WORK_ITEM_ID = 'work_item_id';
    public const AUTHOR_ID    = 'author_id';
    public const HOURS        = 'hours';
    public const DATE         = 'date';
    public const NOTE         = 'note';
    public const CREATED_AT   = 'created_at';
    public const UPDATED_AT   = 'updated_at';

    protected $table = self::TABLE;

    protected $fillable = [
        self::WORK_ITEM_ID,
        self::AUTHOR_ID,
        self::HOURS,
        self::DATE,
        self::NOTE
    ];

    protected $casts = [
        self::DATE => 'datetime'
    ];

    /**
     * The project this time entry belongs to
     *
     * @return HasOneThrough<Project>
     */
    public function project(): HasOneThrough
    {
        return $this->hasOneThrough(
            Project::class,
            WorkItem::class,
            WorkItem::ID,
            Project::ID,
            self::WORK_ITEM_ID,
            WorkItem::PROJECT_ID
        );
    }

    /**
     * The work item this time entry belongs to
     *
     * @return BelongsTo<WorkItem>
     */
    public function work_item(): BelongsTo
    {
        return $this->belongsTo(
            WorkItem::class,
            self::WORK_ITEM_ID,
            WorkItem::ID
        );
    }

    /**
     * Filter time entries by work item id
     *
     * @param Builder $query
     * @param int     $workItemId
     *
     * @return void
     */
    public function scopeWhereWorkItem(Builder $query, int $workItemId): void
    {
        $query->where(self::WORK_ITEM_ID, $workItemId);
    }

    /**
     * The author of the time entry
     *
     * @return BelongsTo<User>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            self::AUTHOR_ID,
            User::ID
        );
    }

    /**
     * Filter time entries by author id
     *
     * @param Builder $query
     * @param int     $authorId
     *
     * @return void
     */
    public function scopeWhereAuthor(Builder $query, int $authorId): void
    {
        $query->where(self::AUTHOR_ID, $authorId);
    }
}
