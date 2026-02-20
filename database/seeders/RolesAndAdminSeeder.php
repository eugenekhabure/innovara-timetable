<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole   = Role::firstOrCreate(['name' => 'admin']);
        $teacherRole = Role::firstOrCreate(['name' => 'teacher']);

        // Create default admin user (update credentials as needed)
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.test'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('admin12345'),
            ]
        );

        // Assign role
        if (!$admin->hasRole($adminRole->name)) {
            $admin->assignRole($adminRole);
        }
    }
}
