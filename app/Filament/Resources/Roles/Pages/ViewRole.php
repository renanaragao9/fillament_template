<?php

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewRole extends ViewRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informações do Perfil')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nome'),

                        TextEntry::make('description')
                            ->label('Descrição')
                            ->placeholder('-'),

                        TextEntry::make('created_at')
                            ->label('Criado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('updated_at')
                            ->label('Atualizado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
