<?php

namespace App\Http\Requests\Api\V1\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();
        $companyId = $user && $user->is_super_admin
            ? $this->input('company_id')
            : $user?->company_id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(fn ($query) => $query->where('company_id', $companyId)),
            ],
            'description' => ['nullable', 'string'],
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer'],
        ];
    }
}
