<?php

namespace App\Models\Scopes;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenant = TenantContext::current();

        if (! $tenant->shouldScope()) {
            return;
        }

        $table = $model->getTable();

        $builder->where(function (Builder $query) use ($model, $table, $tenant) {
            $query->where("{$table}.company_id", $tenant->companyId());

            if ($model->tenantScopeIncludesGlobalRecords()) {
                $query->orWhereNull("{$table}.company_id");
            }
        });
    }
}
