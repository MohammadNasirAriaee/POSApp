<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
	 */
	public function rules(): array
	{
		return [
			'first_name' => ['required', 'string', 'max:100'],
			'last_name' => ['required', 'string', 'max:100'],
			'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
			'phone' => ['nullable', 'string', 'max:20'],
			'address' => ['nullable', 'string', 'max:500'],
			'position' => ['required', 'string', 'max:100'],
			'salary' => ['nullable', 'numeric', 'min:0'],
			'hire_date' => ['nullable', 'date'],


            
			'status' => ['nullable', 'in:active,inactive'],
		];
	}
}
