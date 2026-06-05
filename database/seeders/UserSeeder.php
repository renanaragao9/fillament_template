<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();

        User::updateOrCreate(
            ['email' => 'renanaragao159@gmail.com'],
            [
                'name' => 'Renan Aragao',
                'password' => bcrypt('12345678'),
                'phone' => null,
                'status' => 'active',
                'role_id' => $adminRole?->id,
                'email_verified_at' => now(),
                'last_login_at' => null,
            ]
        );
    }
}
