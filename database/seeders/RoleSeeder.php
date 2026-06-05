<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::updateOrCreate(
            ['name' => 'Admin'],
            ['description' => 'Acesso total ao sistema']
        );

        $allPermissions = Permission::pluck('id');

        $admin->permissions()->sync($allPermissions);
    }
}
