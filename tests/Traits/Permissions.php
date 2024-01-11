<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Permissions as SystemPermissions;
use App\Models\Roles as SystemRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

trait Permissions
{
    public function buildPermissions(): void
    {
        $this->app->make(PermissionRegistrar::class)->forgetCachedPermissions();
        foreach (SystemPermissions::LIST as $item) {
            Permission::findOrCreate($item);
        }
        foreach (SystemRoles::PERMISSIONS as $role => $perms) {
            $role = Role::findOrCreate($role);
            foreach ($perms as $p) {
                $role->givePermissionTo($p);
            }
        }
    }
}
