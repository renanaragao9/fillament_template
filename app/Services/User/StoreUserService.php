<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;

class StoreUserService
{
    public function run(array $data): ?User
    {
        if (! empty($data['role_id']) && ! Role::find($data['role_id'])) {
            return null;
        }

        return User::create($data)->load('role');
    }
}
