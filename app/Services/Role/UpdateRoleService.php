<?php

namespace App\Services\Role;

use App\Models\Permission;
use App\Models\Role;

class UpdateRoleService
{
    public function run(Role $role, array $data): Role
    {
        if (array_key_exists('permission_ids', $data)) {
            $permissionIds = Permission::whereIn('id', $data['permission_ids'])->pluck('id');
            $role->permissions()->sync($permissionIds);
            unset($data['permission_ids']);
        }

        $role->update($data);

        return $role->load('permissions');
    }
}
