<?php

namespace App\Models\Scopes;

use App\Support\TenantContext;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class SystemRecordScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (! TenantContext::current()->shouldScope()) {
            return;
        }

        $builder->where($model->getTable().'.is_super_admin', false);
    }
}
