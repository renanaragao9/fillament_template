<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    abstract protected function resourceCode(): string;

    protected function hasPermission(User $user, string $action): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        return $user->role?->permissions()
            ->where('code', "{$this->resourceCode()}.{$action}")
            ->exists() ?? false;
    }

    protected function ownsRecord(User $user, Model $model): bool
    {
        if ($user->is_super_admin) {
            return true;
        }

        if (! array_key_exists('company_id', $model->getAttributes())) {
            return true;
        }

        return $model->company_id !== null
            && $model->company_id === $user->company_id;
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
        return $this->hasPermission($user, 'update') && $this->ownsRecord($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'delete') && $this->ownsRecord($user, $model);
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'update') && $this->ownsRecord($user, $model);
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->hasPermission($user, 'delete') && $this->ownsRecord($user, $model);
    }
}
