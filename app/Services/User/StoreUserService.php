<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use App\Support\TenantContext;

class StoreUserService
{
    public function run(array $data): ?User
    {
        if (! empty($data['role_id'])) {
            $role = Role::find($data['role_id']);

            if (! $role || $role->company_id !== $this->companyIdFor($data)) {
                return null;
            }
        }

        return User::create($data)->load('role');
    }

    protected function companyIdFor(array $data): ?int
    {
        $tenant = TenantContext::current();

        if ($tenant->shouldScope()) {
            return $tenant->companyId();
        }

        return isset($data['company_id']) ? (int) $data['company_id'] : null;
    }
}
