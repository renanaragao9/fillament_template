<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class PermissionPolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'permission';
    }

    public function create(User $user): bool
    {
        return (bool) $user->is_super_admin;
    }

    public function update(User $user, Model $model): bool
    {
        return (bool) $user->is_super_admin;
    }

    public function delete(User $user, Model $model): bool
    {
        return (bool) $user->is_super_admin;
    }

    public function restore(User $user, Model $model): bool
    {
        return (bool) $user->is_super_admin;
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return (bool) $user->is_super_admin;
    }
}
