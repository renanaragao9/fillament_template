<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;
use App\Support\TenantContext;

class UpdateUserService
{
    public function run(User $user, array $data): ?User
    {
        if (! empty($data['role_id'])) {
            $role = Role::find($data['role_id']);

            if (! $role || $role->company_id !== $this->companyIdFor($user, $data)) {
                return null;
            }
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user->load('role');
    }

    protected function companyIdFor(User $user, array $data): ?int
    {
        $tenant = TenantContext::current();

        if ($tenant->shouldScope()) {
            return $tenant->companyId();
        }

        return isset($data['company_id']) ? (int) $data['company_id'] : $user->company_id;
    }
}
