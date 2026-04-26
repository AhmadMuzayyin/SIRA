<?php

namespace App\Http\Requests;

use App\Models\ViolationCriterion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ViolationCriterionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $criterion = $this->route('violation_criterion');
        $criterionId = $criterion instanceof ViolationCriterion ? $criterion->id : null;

        return [
            'code' => ['required', 'string', 'max:255', Rule::unique('violation_criteria', 'code')->ignore($criterionId)],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(['ringan', 'sedang', 'berat'])],
            'weight' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
