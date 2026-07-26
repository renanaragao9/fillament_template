<?php

namespace App\Models\Concerns;

use App\Models\Scopes\TenantScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * @mixin Model
 *
 * @property int|null $company_id
 * @property bool $is_super_admin
 *
 * @method static void addGlobalScope(mixed $scope, mixed $implementation = null)
 * @method static void saving(callable $callback)
 */
trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope);

        static::saving(function (Model $model): void {
            if (! Auth::hasUser()) {
                return;
            }

            $user = Auth::user();

            if ($user && ! $user->is_super_admin) {
                $model->company_id = $user->company_id;
                $model->is_super_admin = false;
            }
        });
    }

    public function tenantScopeIncludesGlobalRecords(): bool
    {
        return false;
    }
}
