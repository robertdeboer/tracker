<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A project user
 *
 * @property int    $id
 * @property int    $project_id
 * @property int    $user_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ProjectUser extends Model
{
    use HasFactory;

    public const TABLE      = 'project_users';
    public const ID         = 'id';
    public const PROJECT_ID = 'project_id';
    public const USER_ID    = 'user_id';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    protected $table = self::TABLE;

    /**
     * Fields that are editable en masse
     *
     * @var array<string>
     */
    protected $fillable = [
        self::PROJECT_ID,
        self::USER_ID
    ];
}
