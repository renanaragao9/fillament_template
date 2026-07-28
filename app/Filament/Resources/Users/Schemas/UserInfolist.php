<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados do Usuário')
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false)
                    ->schema([
                        ImageEntry::make('image_path')
                            ->label('Avatar')
                            ->circular()
                            ->columnSpanFull(),

                        TextEntry::make('name')
                            ->label('Nome'),

                        TextEntry::make('email')
                            ->label('E-mail'),

                        TextEntry::make('phone')
                            ->label('Telefone')
                            ->placeholder('-'),

                        TextEntry::make('role.name')
                            ->label('Perfil')
                            ->placeholder('-'),

                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                                default => $state,
                            })
                            ->color(fn ($state) => match ($state) {
                                'active' => 'success',
                                'inactive' => 'danger',
                                default => 'gray',
                            })
                            ->placeholder('-'),

                        TextEntry::make('email_verified_at')
                            ->label('E-mail verificado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('-'),

                        TextEntry::make('last_login_at')
                            ->label('Último acesso')
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
            ]);
    }
}
