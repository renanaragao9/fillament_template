<?php

namespace App\Http\Resources\Api\V1\Company;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'logo_url' => $this->logo_path ? storage_url($this->logo_path) : null,
            'email' => $this->email,
            'phone' => $this->phone,
            'document' => $this->document,
            'status' => $this->status,
            'settings' => $this->settings,
            'trial_ends_at' => $this->trial_ends_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
