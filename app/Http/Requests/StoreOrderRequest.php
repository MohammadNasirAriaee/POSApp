<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount' => ['required', 'numeric', 'min:0'],
        ];
    }
}
