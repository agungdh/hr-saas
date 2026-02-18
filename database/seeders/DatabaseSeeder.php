<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create super admin for central admin access
        $this->call(SuperAdminSeeder::class);

        // Create demo tenants with users
        $this->call(DemoTenantSeeder::class);
    }
}
