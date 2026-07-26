<?php

namespace App\Http\Requests\Api\V1\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:8'],
            'status' => ['nullable', 'in:active,inactive'],
            'role_id' => ['nullable', 'integer'],
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
        ];
    }
}
