<?php

namespace App\Services\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class IndexUserService
{
    public function run(): Collection
    {
        return User::with('role')->get();
    }
}
