<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'violation_id' => ['required', 'exists:violations,id'],
            'occurred_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
