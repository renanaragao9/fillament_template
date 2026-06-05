<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Traits\HandlesRolePermissions;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    use HandlesRolePermissions;

    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        $this->savePermissions();
    }
}
