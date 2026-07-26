<?php

namespace App\Services\Permission;

use App\Models\Permission;

class StorePermissionService
{
    public function run(array $data): Permission
    {
        return Permission::create($data);
    }
}
