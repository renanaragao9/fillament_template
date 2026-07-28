<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Filters\Common\CreatedAtFilter;
use App\Filament\Filters\User\RoleFilter;
use App\Filament\Filters\User\StatusFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=&background=random'),
                TextColumn::make('name')
                    ->label('Nome')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Telefone')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
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
                TextColumn::make('role.name')
                    ->label('Perfil')
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('last_login_at')
                    ->label('Último acesso')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email_verified_at')
                    ->label('E-mail verificado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                StatusFilter::make(),
                RoleFilter::make(),
                CreatedAtFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon(Heroicon::OutlinedEye),
                EditAction::make()
                    ->icon(Heroicon::OutlinedPencil),
                DeleteAction::make()
                    ->icon(Heroicon::OutlinedTrash),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
