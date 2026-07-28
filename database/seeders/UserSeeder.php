<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::updateOrCreate(
            ['slug' => 'seuracha-store'],
            [
                'name' => 'Seuracha Store',
                'status' => 'active',
            ]
        );

        $adminRole = Role::updateOrCreate(
            ['company_id' => $company->id, 'name' => 'Admin'],
            ['description' => 'Acesso total ao sistema', 'is_super_admin' => false]
        );

        $adminRole->permissions()->sync(Permission::pluck('id'));

        User::updateOrCreate(
            ['email' => 'admin@seuracha.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('12345678'),
                'phone' => null,
                'status' => 'active',
                'is_super_admin' => true,
                'company_id' => $company->id,
                'role_id' => $adminRole->id,
                'email_verified_at' => now(),
                'last_login_at' => null,
            ]
        );
    }
}
