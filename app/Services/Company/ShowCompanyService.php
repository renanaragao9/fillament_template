<?php

namespace App\Services\Company;

use App\Models\Company;

class ShowCompanyService
{
    public function run(Company $company): Company
    {
        return $company;
    }
}
