<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cached roles and permissions
        app()[ \Spatie\Permission\PermissionRegistrar::class ]->forgetCachedPermissions();

        $all_roles = [
            [ 'name' => 'admin' ],
        ];

        $all_permissions = (array) config('permission.permissions');
        $permissions = [];
        foreach( $all_permissions as $permission ) {
            $permissions[] = Permission::firstOrCreate($permission);
        }

        $roles = [];
        foreach( $all_roles as $role ) {
            $roles[] = $curerent_role = Role::firstOrCreate($role);
            $curerent_role->givePermissionTo($permissions);
        }

        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
