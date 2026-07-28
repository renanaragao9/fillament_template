<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPermission extends ViewRecord
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon(Heroicon::OutlinedPencil),
        ];
    }
}
