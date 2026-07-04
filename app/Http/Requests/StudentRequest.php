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
            'gender' => ['nullable', 'string', 'max:50', Rule::in(['L', 'P'])],
            'room' => ['required_without:lembaga', 'nullable', 'string', Rule::in(array_keys(Student::ROOMS))],
            'lembaga' => ['required_without:room', 'nullable', 'string', Rule::in(array_keys(Student::LEMBAGAS))],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        // If type is switched, ensure the other is null so it's not saved.
        if ($this->has('type')) {
            if ($this->input('type') === 'room') {
                $this->merge(['lembaga' => null]);
            } elseif ($this->input('type') === 'lembaga') {
                $this->merge(['room' => null]);
            }
        }
    }
}
