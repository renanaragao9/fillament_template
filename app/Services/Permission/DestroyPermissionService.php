<?php

namespace App\Services\Permission;

use App\Models\Permission;

class DestroyPermissionService
{
    public function run(Permission $permission): void
    {
        $permission->delete();
    }
}
