<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create permissions
        $permissions = [
            'view consumers',
            'create consumers',
            'edit consumers',
            'delete consumers',

            'view properties',
            'create properties',
            'edit properties',
            'delete properties',

            'view equipment',
            'create equipment',
            'edit equipment',
            'delete equipment',

            'view consumer usages',
            'create consumer usages',
            'edit consumer usages',
            'delete consumer usages',

            'manage roles',
            'manage permissions',
            'manage users',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign created permissions
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleManager = Role::firstOrCreate(['name' => 'manager']);
        $roleROOfficer = Role::firstOrCreate(['name' => 'ro_officer']);
        $roleUser = Role::firstOrCreate(['name' => 'user']);

        // Assign all permissions to admin
        $roleAdmin->givePermissionTo(Permission::all());

        // Assign specific permissions to manager
        $roleManager->givePermissionTo([
            'view consumers',
            'create consumers',
            'edit consumers',
            'view properties',
            'create properties',
            'edit properties',
            'view equipment',
            'create equipment',
            'edit equipment',
            'view consumer usages',
            'create consumer usages',
            'edit consumer usages',
            'manage users',
        ]);

        // Assign specific permissions to RO Officer
        $roleROOfficer->givePermissionTo([
            'view consumers',
            'create consumers',
            'edit consumers',
            'view properties',
            'create properties',
            'edit properties',
            'view equipment',
            'create equipment',
            'edit equipment',
            'view consumer usages',
            'create consumer usages',
            'edit consumer usages',
        ]);

        // Assign specific permissions to regular user (if any, for now just view)
        $roleUser->givePermissionTo([
            'view consumers',
            'view properties',
            'view equipment',
            'view consumer usages',
        ]);

        $this->command->info('Permissions and roles seeded successfully.');
    }
}