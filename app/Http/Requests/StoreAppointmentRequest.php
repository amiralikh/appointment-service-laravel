<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'health_professional_id' => ['required', 'integer', 'exists:health_professionals,id'],
            'date' => ['required', 'date', 'after:now'],
            'customer_email' => ['required', 'email', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'date.after' => 'The appointment date must be in the future.',
            'service_id.exists' => 'The selected service does not exist.',
            'health_professional_id.exists' => 'The selected health professional does not exist.',
        ];
    }
}
