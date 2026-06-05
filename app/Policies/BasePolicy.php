<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    abstract protected function resourceCode(): string;

    protected function hasPermission(User $user, string $action): bool
    {
        return $user->role?->permissions()
            ->where('code', "{$this->resourceCode()}.{$action}")
            ->exists() ?? false;
    }

    public function viewAny(User $user): bool
    {
        return $this->hasPermission($user, 'view');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->hasPermission($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'update');
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'delete');
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'update');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'delete');
    }
}
