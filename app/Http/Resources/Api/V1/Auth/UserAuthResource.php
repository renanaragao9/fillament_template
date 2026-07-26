<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAuthResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'image_url' => $this->image_path ? storage_url($this->image_path) : null,
            ],

            'token' => $this->createToken('auth_token')->plainTextToken,
        ];
    }
}
