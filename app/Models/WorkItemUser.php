<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A work item user
 *
 * @property int    $id           The internal work item user id
 * @property int    $work_item_Id The work item id
 * @property int    $user_id      The user id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WorkItemUser extends Model
{
    use HasFactory;

    public const TABLE        = 'work_item_users';
    public const ID           = 'id';
    public const WORK_ITEM_ID = 'work_item_id';
    public const USER_ID      = 'user_id';
    public const CREATED_AT   = 'created_at';
    public const UPDATED_AT   = 'updated_at';

    protected $table = self::TABLE;
}
