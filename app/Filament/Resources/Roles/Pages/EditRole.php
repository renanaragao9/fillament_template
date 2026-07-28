<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\Roles\Traits\HandlesRolePermissions;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditRole extends EditRecord
{
    use HandlesRolePermissions;

    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon(Heroicon::OutlinedEye),
            DeleteAction::make()
                ->icon(Heroicon::OutlinedTrash),
        ];
    }

    protected function afterSave(): void
    {
        $this->savePermissions();
    }
}
