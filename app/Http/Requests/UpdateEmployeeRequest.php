<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
	public function authorize(): bool
	{
		return true;
	}

	public function rules(): array
	{
		$employeeId = $this->route('employee')?->id ?? $this->route('employee');



		return [
			'first_name' => ['required', 'string', 'max:100'],
			'last_name' => ['required', 'string', 'max:100'],
			'email' => [
				'required',
				'email',
				'max:255',
				Rule::unique('employees', 'email')->ignore($employeeId),
			],
			'phone' => ['nullable', 'string', 'max:30'],
			'address' => ['nullable', 'string', 'max:500'],
			'position' => ['required', 'string', 'max:100'],
			'salary' => ['nullable', 'numeric', 'min:0'],
			'status' => ['nullable', Rule::in(['active', 'inactive'])],
		];
	}
}

