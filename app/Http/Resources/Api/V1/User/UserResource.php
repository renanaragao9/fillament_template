<?php

namespace App\Http\Resources\Api\V1\User;

use App\Http\Resources\Api\V1\Role\RoleResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'image_url' => $this->image_path ? storage_url($this->image_path) : null,
            'company_id' => $this->company_id,
            'role_id' => $this->role_id,
            'role' => new RoleResource($this->whenLoaded('role')),
            'last_login_at' => $this->last_login_at,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
