<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\Company;
use App\Models\Role;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Section::make('Dados do Usuário')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('image_path')
                            ->label('Avatar')
                            ->image()
                            ->directory(fn (Get $get) => 'avatars/'.static::tenantFolder($get))
                            ->columnSpanFull(),

                        TextInput::make('name')
                            ->label('Nome')
                            ->required(),

                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required(),

                        TextInput::make('phone')
                            ->label('Telefone')
                            ->mask('(99) 9-9999-9999')
                            ->nullable(),

                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Ativo',
                                'inactive' => 'Inativo',
                            ])
                            ->nullable(),

                        Select::make('company_id')
                            ->label('Empresa')
                            ->options(fn () => Company::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->visible(fn () => (bool) Auth::user()?->is_super_admin),

                        Select::make('role_id')
                            ->label('Perfil')
                            ->options(fn (Get $get) => Role::query()
                                ->where('company_id', $get('company_id') ?: Auth::user()?->company_id)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->nullable(),

                        DateTimePicker::make('email_verified_at')
                            ->label('E-mail verificado em'),

                        TextInput::make('password')
                            ->label('Senha')
                            ->password()
                            ->required(fn ($record) => $record === null)
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected static function tenantFolder(Get $get): string
    {
        return (string) ($get('company_id') ?: Auth::user()?->company_id ?: 'system');
    }
}
