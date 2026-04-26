<?php

namespace App\Exports;

use App\Models\StudentViolation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentViolationsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return StudentViolation::query()
            ->with(['student', 'violation'])
            ->orderByDesc('occurred_at')
            ->get()
            ->map(fn (StudentViolation $studentViolation): array => [
                'occurred_at' => $studentViolation->occurred_at?->toDateString(),
                'student_nis' => $studentViolation->student?->nis,
                'student_name' => $studentViolation->student?->name,
                'violation_code' => $studentViolation->violation?->code,
                'violation_name' => $studentViolation->violation?->name,
                'notes' => $studentViolation->notes,
            ]);
    }

    public function headings(): array
    {
        return ['occurred_at', 'student_nis', 'student_name', 'violation_code', 'violation_name', 'notes'];
    }
}
