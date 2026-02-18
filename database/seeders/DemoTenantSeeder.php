<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Exceptions\TenantDatabaseAlreadyExistsException;

class DemoTenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'id' => 'acme',
                'name' => 'Acme Corporation',
                'slug' => 'acme',
                'domain' => 'acme.hr-saas.test',
                'plan' => 'pro',
                'users' => [
                    ['name' => 'John Admin', 'email' => 'john@acme.test'],
                    ['name' => 'Jane Manager', 'email' => 'jane@acme.test'],
                    ['name' => 'Bob Employee', 'email' => 'bob@acme.test'],
                ],
            ],
            [
                'id' => 'startup',
                'name' => 'Startup XYZ',
                'slug' => 'startup',
                'domain' => 'startup.hr-saas.test',
                'plan' => 'free',
                'users' => [
                    ['name' => 'Alice Founder', 'email' => 'alice@startup.test'],
                    ['name' => 'Charlie Dev', 'email' => 'charlie@startup.test'],
                ],
            ],
        ];

        foreach ($tenants as $tenantData) {
            $this->createTenant($tenantData);
        }
    }

    /**
     * Create a tenant with users.
     */
    protected function createTenant(array $data): void
    {
        $users = $data['users'];
        unset($data['users']);

        // Check if tenant already exists in database
        $existingTenant = Tenant::find($data['id']);

        if ($existingTenant) {
            // Tenant exists, just add users
            $tenant = $existingTenant;
        } else {
            // Check if schema exists and clean it up
            $schemaName = 'tenant' . $data['id'];
            $schemaExists = DB::select(
                "SELECT schema_name FROM information_schema.schemata WHERE schema_name = ?",
                [$schemaName]
            );

            if ($schemaExists) {
                DB::statement("DROP SCHEMA IF EXISTS $schemaName CASCADE");
            }

            // Create new tenant
            try {
                $tenant = Tenant::create($data);
            } catch (TenantDatabaseAlreadyExistsException $e) {
                // If database still exists, force delete and recreate
                DB::statement("DROP SCHEMA IF EXISTS $schemaName CASCADE");
                $tenant = Tenant::create($data);
            }

            // Create domain
            $tenant->createDomain($data['domain']);
        }

        // Create users in tenant context
        tenancy()->initialize($tenant);

        foreach ($users as $userData) {
            DB::table('users')->updateOrInsert(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        tenancy()->end();
    }
}
