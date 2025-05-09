<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::create(['name' => 'super_admin']);
        Role::create(['name' => 'admin']);
        $internRole = Role::create(['name' => 'intern']);

        // Create test user with intern role
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'role_id' => $internRole->id,
        ]);

        // Create admin user with admin role
        Admin::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role_id' => $adminRole->id,
        ]);

        
        // Create CRUD permissions for managing resources
        $permissions = [

            Permission::create(['permission' => 'create_interns']),
            Permission::create(['permission' => 'read_interns']),
            Permission::create(['permission' => 'update_interns']),
            Permission::create(['permission' => 'delete_interns']),
            
            Permission::create(['permission' => 'create_tasks']),
            Permission::create(['permission' => 'read_tasks']),
            Permission::create(['permission' => 'update_tasks']),
            Permission::create(['permission' => 'delete_tasks']),
            
            Permission::create(['permission' => 'create_admins']),
            Permission::create(['permission' => 'read_admins']),
            Permission::create(['permission' => 'update_admins']),
            Permission::create(['permission' => 'delete_admins'])
        ];
    }
}
