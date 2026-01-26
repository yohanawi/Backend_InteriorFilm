<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User permissions
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'user-view',

            // Role permissions
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'role-view',

            // Permission permissions
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            'permission-view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web'
        ]);
        $adminRole->givePermissionTo(Permission::all());

        // Create Manager role with limited permissions
        $managerRole = Role::firstOrCreate([
            'name' => 'Manager',
            'guard_name' => 'web'
        ]);

        $managerPermissions = [
            'user-list',
            'user-view',
            'user-edit',
            'role-list',
            'role-view',
            'permission-list',
            'permission-view',
        ];

        $managerRole->givePermissionTo($managerPermissions);

        $this->command->info('Roles and Permissions created successfully!');
    }
}
