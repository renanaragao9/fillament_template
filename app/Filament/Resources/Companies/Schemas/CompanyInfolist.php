<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\KeyValueEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CompanyInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados da Empresa')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ImageEntry::make('logo_path')
                            ->label('Logo')
                            ->circular()
                            ->columnSpanFull()
                            ->placeholder('-'),

                        TextEntry::make('name')
                            ->label('Nome'),

                        TextEntry::make('slug')
                            ->label('Slug')
                            ->badge(),

                        TextEntry::make('domain')
                            ->label('Domínio')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                                'suspended' => 'Suspenso',
                                default => $state,
                            })
                            ->color(fn ($state) => match ($state) {
                                'active' => 'success',
                                'inactive' => 'gray',
                                'suspended' => 'danger',
                                default => 'gray',
                            }),

                        TextEntry::make('email')
                            ->label('E-mail')
                            ->placeholder('-'),

                        TextEntry::make('phone')
                            ->label('Telefone')
                            ->placeholder('-'),

                        TextEntry::make('document')
                            ->label('CNPJ/CPF')
                            ->placeholder('-'),

                        TextEntry::make('trial_ends_at')
                            ->label('Fim do período de teste')
                            ->dateTime('d/m/Y H:i')
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

                Section::make('Configurações')
                    ->collapsible()
                    ->collapsed(true)
                    ->schema([
                        KeyValueEntry::make('settings')
                            ->label('Configurações')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
