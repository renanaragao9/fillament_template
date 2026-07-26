<?php

namespace App\Http\Requests\Api\V1\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8'],
            'status' => ['sometimes', 'in:active,inactive'],
            'role_id' => ['nullable', 'integer'],
            'company_id' => ['nullable', Rule::exists('companies', 'id')],
        ];
    }
}
