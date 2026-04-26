<?php

namespace App\Exports;

use App\Models\StudentPrediction;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PredictionsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return StudentPrediction::query()
            ->with(['student', 'suggestedViolation'])
            ->orderByDesc('rank_score')
            ->get()
            ->map(fn (StudentPrediction $prediction): array => [
                'nis' => $prediction->student?->nis,
                'student_name' => $prediction->student?->name,
                'risk_probability' => (float) $prediction->risk_probability,
                'predicted_to_reoffend' => $prediction->predicted_to_reoffend ? 'ya' : 'tidak',
                'rank_score' => $prediction->rank_score,
                'suggested_violation' => $prediction->suggestedViolation?->name,
                'predicted_at' => $prediction->predicted_at?->toDateTimeString(),
            ]);
    }

    public function headings(): array
    {
        return ['nis', 'student_name', 'risk_probability', 'predicted_to_reoffend', 'rank_score', 'suggested_violation', 'predicted_at'];
    }
}
