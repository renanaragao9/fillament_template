<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginAuthService
{
    public function run(array $data): ?User
    {
        $user = User::query()
            ->when(
                ! empty($data['email']),
                fn ($query) => $query->where('email', $data['email'])
            )
            ->when(
                ! empty($data['phone']),
                fn ($query) => $query->where('phone', $data['phone'])
            )
            ->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return null;
        }

        return $user;
    }
}
