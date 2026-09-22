<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesEmail;
use App\Rules\NormalizedPhoneUnique;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    use NormalizesEmail;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', 'unique:customers,email'],
            'phone' => ['nullable', 'string', 'max:20', new NormalizedPhoneUnique('customers', 'phone')],
            'address' => ['nullable', 'string'],
        ];
    }
}
