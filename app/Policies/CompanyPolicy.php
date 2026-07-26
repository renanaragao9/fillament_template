<?php

namespace App\Policies;

use App\Models\User;

class CompanyPolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'company';
    }

    public function create(User $user): bool
    {
        return $user->is_super_admin;
    }
}
