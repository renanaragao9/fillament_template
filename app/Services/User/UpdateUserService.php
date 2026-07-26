<?php

namespace App\Services\User;

use App\Models\Role;
use App\Models\User;

class UpdateUserService
{
    public function run(User $user, array $data): ?User
    {
        if (! empty($data['role_id']) && ! Role::find($data['role_id'])) {
            return null;
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return $user->load('role');
    }
}
