<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::where('slug', 'seuracha-store')->first();

        $admin = Role::updateOrCreate(
            ['company_id' => $company?->id, 'name' => 'Admin'],
            ['description' => 'Acesso total ao sistema']
        );

        $allPermissions = Permission::pluck('id');

        $admin->permissions()->sync($allPermissions);
    }
}
