<?php

namespace App\Services\User;

use App\Models\User;

class DestroyUserService
{
    public function run(User $user): void
    {
        $user->delete();
    }
}
