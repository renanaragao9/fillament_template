<?php

namespace App\Services\Company;

use App\Models\Company;

class UpdateCompanyService
{
    public function run(Company $company, array $data): Company
    {
        $company->update($data);

        return $company;
    }
}
