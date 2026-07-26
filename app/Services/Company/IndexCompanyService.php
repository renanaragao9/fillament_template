<?php

namespace App\Services\Company;

use App\Models\Company;
use Illuminate\Database\Eloquent\Collection;

class IndexCompanyService
{
    public function run(): Collection
    {
        return Company::query()->get();
    }
}
