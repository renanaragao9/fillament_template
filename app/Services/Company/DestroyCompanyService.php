<?php

namespace App\Services\Company;

use App\Models\Company;

class DestroyCompanyService
{
    public function run(Company $company): void
    {
        $company->delete();
    }
}
