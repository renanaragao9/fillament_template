<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Student;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
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
                    ->schema([
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

                Section::make('Alunos Gerenciados')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        RepeatableEntry::make('students')
                            ->label('Alunos')
                            ->contained(false)
                            ->columns(4)
                            ->schema([
                                ImageEntry::make('image_path')
                                    ->label('')
                                    ->circular()
                                    ->size(40)
                                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=&background=random')
                                    ->placeholder('-'),

                                TextEntry::make('name')
                                    ->label('Nome')
                                    ->weight('medium')
                                    ->url(fn ($record) => $record
                                        ? route('filament.admin.resources.students.view', $record)
                                        : null
                                    ),

                                TextEntry::make('code')
                                    ->label('Código')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('status')
                                    ->label('Status')
                                    ->badge()
                                    ->formatStateUsing(fn ($state) => Student::STATUSES[$state] ?? $state)
                                    ->color(fn ($state) => match ($state) {
                                        'active' => 'success',
                                        'inactive' => 'gray',
                                        'suspended' => 'danger',
                                        'pending' => 'warning',
                                        default => 'gray',
                                    }),
                            ]),
                    ]),
            ]);
    }
}
