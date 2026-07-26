<?php

namespace App\Services\Permission;

use App\Models\Permission;

class ShowPermissionService
{
    public function run(Permission $permission): Permission
    {
        return $permission;
    }
}
