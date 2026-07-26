<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Tenancy
            CompanySeeder::class,

            // Controle de acesso
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
