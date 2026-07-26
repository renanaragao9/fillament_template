<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginAuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['sometimes', 'required_without:phone', 'string', 'email'],
            'phone' => ['sometimes', 'required_without:email', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'O campo email deve ser um endereço de email válido.',
            'email.required_without' => 'Informe o email ou telefone.',
            'phone.required_without' => 'Informe o email ou telefone.',
            'password.required' => 'O campo senha é obrigatório.',
        ];
    }
}
