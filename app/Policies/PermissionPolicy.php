<?php

namespace App\Policies;

class PermissionPolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'permission';
    }
}
