<?php

namespace App\Policies;

class UserPolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'user';
    }
}
