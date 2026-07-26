<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::updateOrCreate(
            ['slug' => 'seuracha-store'],
            [
                'name' => 'Seuracha Store',
                'domain' => null,
                'email' => 'admin@seuracha.com',
                'status' => 'active',
            ]
        );
    }
}
