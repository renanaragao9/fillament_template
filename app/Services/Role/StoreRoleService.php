<?php

namespace App\Services\Role;

use App\Models\Permission;
use App\Models\Role;

class StoreRoleService
{
    public function run(array $data): Role
    {
        $permissionIds = Permission::whereIn('id', $data['permission_ids'] ?? [])->pluck('id');
        unset($data['permission_ids']);

        $role = Role::create($data);
        $role->permissions()->sync($permissionIds);

        return $role->load('permissions');
    }
}
