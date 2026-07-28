<?php

namespace App\Filament\Filters\Common;

use Filament\Forms\Components\DatePicker;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class CreatedAtFilter
{
    public static function make(): Filter
    {
        return Filter::make('created_at')
            ->label('Criado em')
            ->form([
                DatePicker::make('created_from')
                    ->label('Criado De:'),
                DatePicker::make('created_until')
                    ->label('Criado Até:'),
            ])
            ->query(function (Builder $query, array $data): Builder {
                return $query
                    ->when(
                        $data['created_from'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                    )
                    ->when(
                        $data['created_until'],
                        fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                    );
            });
    }
}
