<?php

namespace App\Http\Requests;

use App\Models\Violation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ViolationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $violation = $this->route('violation');
        $violationId = $violation instanceof Violation ? $violation->id : null;

        return [
            'violation_criterion_id' => ['required', 'exists:violation_criteria,id'],
            'code' => ['required', 'string', 'max:255', Rule::unique('violations', 'code')->ignore($violationId)],
            'name' => ['required', 'string', 'max:255'],
            'points' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
