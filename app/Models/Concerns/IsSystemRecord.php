<?php

namespace App\Models\Concerns;

use App\Models\Scopes\SystemRecordScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 *
 * @property bool $is_super_admin
 *
 * @method static void addGlobalScope(mixed $scope, mixed $implementation = null)
 * @method static void saving(callable $callback)
 */
trait IsSystemRecord
{
    protected static function bootIsSystemRecord(): void
    {
        static::addGlobalScope(new SystemRecordScope);

        static::saving(function (Model $model): void {
            if (TenantContext::current()->shouldScope()) {
                $model->is_super_admin = false;
            }
        });
    }
}
