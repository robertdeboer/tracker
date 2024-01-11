<?php

declare(strict_types=1);

namespace App\GraphQL\Mutations\User;

use App\Exceptions\DataException;
use App\Models\User;
use Spatie\Permission\Models\Role;

final readonly class AssignRole
{
    /**
     * @param null  $_
     * @param array $args
     *
     * @return User
     * @throws DataException
     */
    public function __invoke(null $_, array $args): User
    {
        $user = User::find($args['user_id']);
        if (!$user instanceof User) {
            throw new DataException('This user does not exist.');
        }
        $role = Role::find($args['role_id']);
        if (!$role instanceof Role) {
            throw new DataException('This role does not exist.');
        }
        $currentRole = $user->getRoleNames()->count() > 0 ? $user->getRoleNames()->first() : null;
        if ($currentRole == $role->name) {
            return $user;
        }
        if (!empty($currentRole)) {
            $user->removeRole($currentRole);
        }
        $user->assignRole($role->name);
        return $user;
    }
}
