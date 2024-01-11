<?php

namespace App\Console\Commands;

use App\Models\Permissions as SystemPermissions;
use App\Models\Roles as SystemRoles;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Console\Command;

class Permissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
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
