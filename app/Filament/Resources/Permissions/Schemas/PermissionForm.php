<?php

namespace App\Filament\Resources\Permissions\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da Permissão')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->columnSpanFull(),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
