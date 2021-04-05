<?php

use App\Permission;
use App\Role;
use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    public function run()
    {
        $admin_permissions = Permission::all();
        Role::findOrFail(1)->permissions()->sync($admin_permissions->pluck('id'));

        $manager_permissions = $admin_permissions->filter(function ($permission) {
            return substr($permission->title, 0, 5) != 'role_'
                && substr($permission->title, 0, 11) != 'permission_'
                && substr($permission->title, 0, 5) != 'team_';
        });
        Role::findOrFail(2)->permissions()->sync($manager_permissions);

        $user_permissions = $admin_permissions->filter(function ($permission) {
            return substr($permission->title, 0, 5) != 'user_'
                && substr($permission->title, 0, 5) != 'role_'
                && substr($permission->title, 0, 11) != 'permission_'
                && substr($permission->title, 0, 6) != 'asset_'
                && substr($permission->title, 0, 5) != 'team_'
                && substr($permission->title, 0, 7) != 'branch_';
        });
        Role::findOrFail(3)->permissions()->sync($user_permissions);

    }
}
