<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'seuracha-store')->first();
        $adminRole = Role::where('company_id', $company?->id)->where('name', 'Admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@seuracha.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('12345678'),
                'phone' => null,
                'status' => 'active',
                'company_id' => $company?->id,
                'role_id' => $adminRole?->id,
                'email_verified_at' => now(),
                'last_login_at' => null,
            ]
        );
    }
}
