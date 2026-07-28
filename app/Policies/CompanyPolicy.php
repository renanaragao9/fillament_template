<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CompanyPolicy extends BasePolicy
{
    protected function resourceCode(): string
    {
        return 'company';
    }

    protected function ownsRecord(User $user, Model $model): bool
    {
        return (bool) $user->is_super_admin || $model->getKey() === $user->company_id;
    }

    public function create(User $user): bool
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
