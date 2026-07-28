<?php

namespace App\Filament\Filters\User;

use Filament\Tables\Filters\SelectFilter;

class RoleFilter
{
    public static function make(): SelectFilter
    {
        return SelectFilter::make('role')
            ->label('Perfil')
            ->relationship('role', 'name');
    }
}
