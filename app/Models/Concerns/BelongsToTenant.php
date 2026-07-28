<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\TenantScope;
use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 *
 * @property int|null $company_id
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
            $tenant = TenantContext::current();

            if ($tenant->shouldScope()) {
                $model->company_id = $tenant->companyId();
            }
        });
    }

    public function tenantScopeIncludesGlobalRecords(): bool
    {
        return false;
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
