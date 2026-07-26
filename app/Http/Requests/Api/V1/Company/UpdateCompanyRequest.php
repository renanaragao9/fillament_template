<?php

namespace App\Http\Requests\Api\V1\Company;

use App\Models\Company;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCompanyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Company $company */
        $company = $this->route('company');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['sometimes', 'required', 'string', 'max:255', 'alpha_dash', Rule::unique('companies', 'slug')->ignore($company->id)],
            'domain' => ['nullable', 'string', 'max:255', Rule::unique('companies', 'domain')->ignore($company->id)],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'document' => ['nullable', 'string', 'max:30'],
            'status' => ['sometimes', 'in:active,inactive,suspended'],
            'settings' => ['nullable', 'array'],
            'trial_ends_at' => ['nullable', 'date'],
        ];
    }
}
