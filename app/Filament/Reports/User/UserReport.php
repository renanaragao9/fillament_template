<?php

namespace App\Filament\Reports\User;

use App\Filament\Reports\Common\BaseReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserReport extends BaseReport
{
    public function title(): string
    {
        return 'Relatório de Usuários';
    }

    public function headers(): array
    {
        return ['Nome', 'E-mail', 'Telefone', 'Status', 'Perfil', 'Último Acesso', 'Criado em'];
    }

    public function searchableFields(): array
    {
        return ['name', 'email'];
    }

    public function modelClass(): string
    {
        return User::class;
    }

    public function mapRow(Model $record): array
    {
        return [
            $record->name,
            $record->email,
            $record->phone,
            $record->status,
            $record->role?->name,
            $record->last_login_at?->format('d/m/Y H:i'),
            $record->created_at?->format('d/m/Y H:i'),
        ];
    }
}
