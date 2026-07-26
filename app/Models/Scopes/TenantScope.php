<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class TenantScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! Auth::hasUser()) {
            return;
        }

        $user = Auth::user();

        if (! $user || $user->is_super_admin) {
            return;
        }

        $table = $model->getTable();

        $builder->where(function (Builder $query) use ($model, $table, $user) {
            $query->where("{$table}.company_id", $user->company_id);

            if ($model->tenantScopeIncludesGlobalRecords()) {
                $query->orWhereNull("{$table}.company_id");
            }
        });

        $builder->where("{$table}.is_super_admin", false);
    }
}
