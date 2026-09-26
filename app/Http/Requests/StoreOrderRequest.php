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
            // Only shape is validated here - PosController::checkout re-derives
            // price/stock/subtotal from the DB and never trusts these values.
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,card,bank_transfer'],
            'tax_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount' => ['required', 'numeric', 'min:0'],
            'tendered' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
