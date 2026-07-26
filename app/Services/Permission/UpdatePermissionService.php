<?php

namespace App\Services\Permission;

use App\Models\Permission;

class UpdatePermissionService
{
    public function run(Permission $permission, array $data): Permission
    {
        $permission->update($data);

        return $permission;
    }
}
