<?php

namespace App\Filament\Reports\Company;

use App\Filament\Reports\Common\BaseReport;
use App\Models\Company;
use Illuminate\Database\Eloquent\Model;

class CompanyReport extends BaseReport
{
    public function title(): string
    {
        return 'Relatório de Empresas';
    }

    public function headers(): array
    {
        return ['Nome', 'Slug', 'Domínio', 'E-mail', 'Status', 'Fim do Teste', 'Criado em'];
    }

    public function searchableFields(): array
    {
        return ['name', 'slug', 'domain', 'email'];
    }

    public function modelClass(): string
    {
        return Company::class;
    }

    public function mapRow(Model $record): array
    {
        return [
            $record->name,
            $record->slug,
            $record->domain,
            $record->email,
            $record->status,
            $record->trial_ends_at?->format('d/m/Y H:i'),
            $record->created_at?->format('d/m/Y H:i'),
        ];
    }
}
