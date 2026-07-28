<?php

namespace App\Filament\Reports\Permission;

use App\Filament\Reports\Common\BaseReport;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Model;

class PermissionReport extends BaseReport
{
    public function title(): string
    {
        return 'Relatório de Permissões';
    }

    public function headers(): array
    {
        return ['Nome', 'Descrição', 'Criado em'];
    }

    public function searchableFields(): array
    {
        return ['name', 'description'];
    }

    public function modelClass(): string
    {
        return Permission::class;
    }

    public function mapRow(Model $record): array
    {
        return [
            $record->name,
            $record->description,
            $record->created_at?->format('d/m/Y H:i'),
        ];
    }
}
