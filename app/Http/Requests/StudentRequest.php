<?php

namespace App\Http\Requests;

use App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $student = $this->route('student');
        $studentId = $student instanceof Student ? $student->id : null;

        return [
            'nis' => ['required', 'string', 'max:255', Rule::unique('students', 'nis')->ignore($studentId)],
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'string', 'max:50'],
            'room' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }
}
