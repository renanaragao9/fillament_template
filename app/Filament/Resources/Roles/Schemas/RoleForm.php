<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Models\Company;
use App\Models\Permission;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

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
                ->collapsed(false)
                ->compact()
                ->schema([
                    Toggle::make('select_all_'.$groupKey)
                        ->label('Selecionar todos')
                        ->live()
                        ->afterStateHydrated(function ($component, $record) use ($permissions) {
                            if (! $record) {
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
                            $set('group_'.$groupKey, $ids);
                        }),

                    CheckboxList::make('group_'.$groupKey)
                        ->label('Permissões')
                        ->options($permissions->pluck('name', 'id'))
                        ->columns(2)
                        ->live()
                        ->afterStateHydrated(function ($component, $record) use ($permissions) {
                            if (! $record) {
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
                            $set('select_all_'.$groupKey, count(array_intersect($state ?? [], $allIds)) === count($allIds));
                        }),
                ]);
        }

        return $schema
            ->columns(1)
            ->components([
                Section::make('Dados do Perfil')
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        Select::make('company_id')
                            ->label('Empresa')
                            ->options(fn () => Company::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->live()
                            ->visible(fn () => (bool) Auth::user()?->is_super_admin),

                        TextInput::make('name')
                            ->label('Nome')
                            ->required()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where(
                                    'company_id',
                                    $get('company_id') ?: Auth::user()?->company_id
                                ),
                            ),

                        Textarea::make('description')
                            ->label('Descrição')
                            ->rows(3)
                            ->nullable(),
                    ]),

                Section::make('Permissões por Grupo')
                    ->description('Selecione os grupos ou permissões individuais para este perfil.')
                    ->schema($permissionSections)
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }
}
