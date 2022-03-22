<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create System Role
        Role::create(['name' => 'Super Administrator']);
        Role::create(['name' => 'Quality Assurance Manager']);
        Role::create(['name' => 'Quality Assurance Coordinator']); // For each department
        Role::create(['name' => 'Staff']); // Academic and support
        Role::create(['name' => 'Member']);
    }
}
