<?php

namespace App\Policies;

class RolePolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'role';
    }
}
