<?php

namespace App\Http\Requests\Api\V1\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:companies,slug'],
            'domain' => ['nullable', 'string', 'max:255', 'unique:companies,domain'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'document' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', 'in:active,inactive,suspended'],
            'settings' => ['nullable', 'array'],
            'trial_ends_at' => ['nullable', 'date'],
        ];
    }
}
