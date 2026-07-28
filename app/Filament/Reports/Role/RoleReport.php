<?php

namespace App\Filament\Reports\Role;

use App\Filament\Reports\Common\BaseReport;
use App\Models\Role;
use Illuminate\Database\Eloquent\Model;

class RoleReport extends BaseReport
{
    public function title(): string
    {
        return 'Relatório de Perfis';
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
        return Role::class;
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
