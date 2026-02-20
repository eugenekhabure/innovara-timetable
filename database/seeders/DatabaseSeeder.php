<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Core roles + first admin user
        $this->call([
            RolesAndAdminSeeder::class,
        ]);
    }
}
