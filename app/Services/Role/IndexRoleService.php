<?php

namespace App\Services\Role;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;

class IndexRoleService
{
    public function run(): Collection
    {
        return Role::with('permissions')->get();
    }
}
