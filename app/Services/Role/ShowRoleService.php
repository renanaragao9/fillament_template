<?php

namespace App\Services\Role;

use App\Models\Role;

class ShowRoleService
{
    public function run(Role $role): Role
    {
        return $role->load('permissions');
    }
}
