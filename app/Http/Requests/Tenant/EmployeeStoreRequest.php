<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeStoreRequest extends FormRequest
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
            'department_id' => ['nullable', 'exists:departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'position' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive', 'on-leave'])],
            'base_salary' => ['required', 'integer', 'min:0'],
            'start_date' => ['required', 'date'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Employee name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email is already in use.',
            'position.required' => 'Position is required.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
            'base_salary.required' => 'Base salary is required.',
            'base_salary.min' => 'Base salary must be at least 0.',
            'start_date.required' => 'Start date is required.',
            'start_date.date' => 'Please provide a valid date.',
        ];
    }
}
