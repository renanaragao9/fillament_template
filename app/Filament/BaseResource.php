<?php

namespace App\Filament;

use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Schema as IlluminateSchema;

abstract class BaseResource extends Resource
{
    public static function canAccess(): bool
    {
        $table = static::$model ? (new static::$model)->getTable() : null;

        if ($table && ! IlluminateSchema::hasTable($table)) {
            return false;
        }

        return parent::canAccess();
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
}
