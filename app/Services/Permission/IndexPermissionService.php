<?php

namespace App\Services\Permission;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;

class IndexPermissionService
{
    public function run(): Collection
    {
        return Permission::query()->get();
    }
}
