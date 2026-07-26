<?php

namespace App\Http\Requests\Api\V1\Permission;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:permissions,code'],
            'group' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
        ];
    }
}
