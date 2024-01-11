<?php

declare(strict_types=1);

namespace App\GraphQL\Queries;

use Spatie\Permission\Models\Role;

final readonly class GetRoles
{
    /**
     * @param null  $_
     * @param array $args
     *
     * @return Role[]
     */
    public function __invoke(null $_, array $args)
    {
        return Role::get(['id', 'name'])->all();
    }
}
