<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * An order
 *
 * @property int                $id               Internal order id
 * @property string             $reference_number External reference number
 * @property Carbon             $date             Date of the order
 * @property string             $email            Email contact for the order
 * @property float              $hours            The number of hours the order was for
 * @property int                $project_id       The project id this order is assigned to
 * @property Carbon             $created_at
 * @property Carbon             $updated_at
 * @property BelongsTo<Project> $project          The project this order belongs to
 */
class Order extends Model
{
    use HasFactory;

    public const TABLE            = 'orders';
    public const ID               = 'id';
    public const REFERENCE_NUMBER = 'reference_number';
    public const DATE             = 'date';
    public const EMAIL            = 'email';
    public const HOURS            = 'hours';
    public const PROJECT_ID       = 'project_id';
    public const CREATED_AT       = 'created_at';
    public const UPDATED_AT       = 'updated_at';

    protected $table = self::TABLE;

    /**
     * The fields that may be modified en masses
     *
     * @var array<string>
     */
    protected $fillable = [
        self::REFERENCE_NUMBER,
        self::DATE,
        self::EMAIL,
        self::HOURS,
        self::PROJECT_ID
    ];

    protected $casts = [

    ];

    /**
     * Filter orders by project id
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
     * The project this order belongs to
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
}
