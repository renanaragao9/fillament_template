<?php

namespace App\Services\Company;

use App\Models\Company;

class StoreCompanyService
{
    public function run(array $data): Company
    {
        return Company::create($data);
    }
}
