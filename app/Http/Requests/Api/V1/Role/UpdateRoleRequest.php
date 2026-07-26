<?php

namespace App\Http\Requests\Api\V1\Role;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Role $role */
        $role = $this->route('role');

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where(fn ($query) => $query->where('company_id', $role->company_id))
                    ->ignore($role->id),
            ],
            'description' => ['nullable', 'string'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer'],
        ];
    }
}
