<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados da Empresa')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set, $record) => $record
                                ? null
                                : $set('slug', Str::slug($state))),

                        TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->alphaDash()
                            ->helperText('Identificador único usado para o tenant (ex: minha-empresa).'),

                        TextInput::make('domain')
                            ->label('Domínio')
                            ->nullable()
                            ->unique(ignoreRecord: true)
                            ->helperText('Domínio próprio, se houver (ex: minhaempresa.com).'),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                                'suspended' => 'Suspenso',
                            ])
                            ->default('active')
                            ->required(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->nullable(),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->mask('(99) 9-9999-9999')
                            ->nullable(),

                        TextInput::make('document')
                            ->label('CNPJ/CPF')
                            ->nullable(),

                        DateTimePicker::make('trial_ends_at')
                            ->label('Fim do período de teste')
                            ->nullable(),

                        FileUpload::make('logo_path')
                            ->label('Logo')
                            ->image()
                            ->directory('companies')
                            ->columnSpanFull()
                            ->nullable(),
                    ]),

                Section::make('Configurações')
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        KeyValue::make('settings')
                            ->label('Configurações')
                            ->keyLabel('Chave')
                            ->valueLabel('Valor')
                            ->nullable(),
                    ]),
            ]);
    }
}
