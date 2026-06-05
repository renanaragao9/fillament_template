<?php

namespace App\Filament\Resources\Roles\RelationManagers;

use App\Models\Permission;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsRelationManager extends RelationManager
{
    protected static string $relationship = 'permissions';

    protected static ?string $title = 'Permissões';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Não precisa de form, apenas exibição e adição via toolbar
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Permissão')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label('Código')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('group')
                    ->label('Grupo')
                    ->badge()
                    ->colors([
                        'blue' => 'Usuários',
                        'purple' => 'Permissões',
                        'indigo' => 'Perfis',
                        'green' => 'Alunos',
                        'cyan' => 'Divisões de Treino',
                        'teal' => 'Exercícios',
                        'emerald' => 'Planos de Refeição',
                        'lime' => 'Avaliações',
                        'yellow' => 'Reservas',
                        'orange' => 'Despesas',
                        'red' => 'Vendas',
                    ])
                    ->sortable(),

                TextColumn::make('description')
                    ->label('Descrição')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Criada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                CreateAction::make()
                    ->label('Adicionar Permissão')
                    ->form(fn (Schema $schema) => $schema
                        ->schema([
                            Select::make('id')
                                ->label('Permissões')
                                ->options(
                                    Permission::whereNotIn('id', $this->getOwnerRecord()->permissions()->pluck('id'))
                                        ->get()
                                        ->mapWithKeys(fn ($permission) => [$permission->id => "{$permission->name} ({$permission->code})"])
                                )
                                ->searchable()
                                ->multiple()
                                ->required(),
                        ])
                    )
                    ->action(fn (array $data) => $this->getOwnerRecord()->permissions()->syncWithoutDetaching($data['id'])),
            ]);
    }
}
