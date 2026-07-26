<?php

namespace App\Http\Requests\Api\V1\Permission;

use App\Models\Permission;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Permission $permission */
        $permission = $this->route('permission');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('permissions', 'code')->ignore($permission->id)],
            'group' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }
}
