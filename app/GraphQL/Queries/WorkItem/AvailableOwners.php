<?php

declare(strict_types=1);

namespace App\GraphQL\Queries\WorkItem;

use App\Models\Roles;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

final readonly class AvailableOwners
{
    /**
     * Get all users that can be an owner of a work item, which is currently any user
     * who is not a customer
     *
     * @param null  $_
     * @param array $args
     *
     * @return Collection<User>
     */
    public function __invoke(null $_, array $args): Collection
    {
        return User::with('roles')->get()->reject(
            fn ($user) => $user->roles->where('name', Roles::CUSTOMER)->count() > 0
        );
    }
}
