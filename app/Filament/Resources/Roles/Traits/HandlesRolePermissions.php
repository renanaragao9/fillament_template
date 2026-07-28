<?php

namespace App\Filament\Resources\Roles\Traits;

use App\Models\Permission;

trait HandlesRolePermissions
{
    protected function savePermissions(): void
    {
        $data = $this->form->getState();
        $permissionIds = [];

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'group_') && is_array($value)) {
                $permissionIds = array_merge($permissionIds, $value);
            }
        }

        $this->record->permissions()->sync(
            Permission::whereIn('id', $permissionIds)->pluck('id')
        );
    }
}
