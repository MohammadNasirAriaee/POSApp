<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesEmail;
use App\Rules\NormalizedPhoneUnique;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
{
    use NormalizesEmail;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')?->id ?? $this->route('customer');

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('customers', 'email')->ignore($this->route('customer'))],
            'phone' => ['nullable', 'string', 'max:20', new NormalizedPhoneUnique('customers', 'phone', $customerId)],
            'address' => ['nullable', 'string'],
        ];
    }
}
