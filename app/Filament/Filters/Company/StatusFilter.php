<?php

namespace App\Filament\Filters\Company;

use Filament\Tables\Filters\SelectFilter;

class StatusFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('status')
            ->label('Status')
            ->options([
                'active' => 'Ativo',
                'inactive' => 'Inativo',
                'suspended' => 'Suspenso',
            ]);
    }
}
