<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        $groups = Permission::all()->groupBy('group');

        $permissionSections = [];

        foreach ($groups as $groupName => $permissions) {
            $groupKey = Str::slug($groupName, '_');

            $permissionSections[] = Section::make($groupName)
                ->collapsible()
                ->collapsed()
                ->compact()
                ->schema([
                    Toggle::make('select_all_' . $groupKey)
                        ->label('Selecionar todos')
                        ->live()
                        ->afterStateHydrated(function ($component, $record) use ($permissions) {
                            if (!$record) {
                                $component->state(false);
                                return;
                            }
                            $rolePermissionIds = $record->permissions->pluck('id')->toArray();
                            $groupPermissionIds = $permissions->pluck('id')->toArray();
                            $allSelected = count(array_intersect($rolePermissionIds, $groupPermissionIds)) === count($groupPermissionIds);
                            $component->state($allSelected);
                        })
                        ->afterStateUpdated(function ($state, $set) use ($permissions, $groupKey) {
                            $ids = $state ? $permissions->pluck('id')->toArray() : [];
                            $set('group_' . $groupKey, $ids);
                        }),

                    CheckboxList::make('group_' . $groupKey)
                        ->label('Permissões')
                        ->options($permissions->pluck('name', 'id'))
                        ->columns(2)
                        ->live()
                        ->afterStateHydrated(function ($component, $record) use ($permissions) {
                            if (!$record) {
                                $component->state([]);
                                return;
                            }
                            $rolePermissionIds = $record->permissions->pluck('id')->toArray();
                            $groupPermissionIds = $permissions->pluck('id')->toArray();
                            $selected = array_values(array_intersect($rolePermissionIds, $groupPermissionIds));
                            $component->state($selected);
                        })
                        ->afterStateUpdated(function ($state, $set) use ($permissions, $groupKey) {
                            $allIds = $permissions->pluck('id')->toArray();
                            $set('select_all_' . $groupKey, count(array_intersect($state ?? [], $allIds)) === count($allIds));
                        }),
                ]);
        }

        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Nome')
                    ->required(),

                Textarea::make('description')
                    ->label('Descrição')
                    ->rows(3)
                    ->nullable(),

                Section::make('Permissões por Grupo')
                    ->description('Selecione os grupos ou permissões individuais para este perfil.')
                    ->schema($permissionSections)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
