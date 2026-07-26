<?php

namespace App\Services\Role;

use App\Models\Role;

class DestroyRoleService
{
    public function run(Role $role): void
    {
        $role->delete();
    }
}
