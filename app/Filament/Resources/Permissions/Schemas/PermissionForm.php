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
                    ->description('Catálogo global: o código é o contrato usado pelas policies (ex: user.view).')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),

                        TextInput::make('code')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->regex('/^[a-z0-9_]+\.[a-z0-9_]+$/')
                            ->helperText('Formato recurso.acao, ex: user.view'),

                        TextInput::make('group')
                            ->label('Grupo')
                            ->required()
                            ->helperText('Usado para agrupar as permissões no perfil.'),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->nullable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
