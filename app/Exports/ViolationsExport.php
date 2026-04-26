<?php

namespace App\Exports;

use App\Models\Violation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ViolationsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Violation::query()
            ->with('criterion')
            ->orderBy('code')
            ->get()
            ->map(fn (Violation $violation): array => [
                'code' => $violation->code,
                'name' => $violation->name,
                'criterion_code' => $violation->criterion?->code,
                'criterion_name' => $violation->criterion?->name,
                'points' => $violation->points,
            ]);
    }

    public function headings(): array
    {
        return ['code', 'name', 'criterion_code', 'criterion_name', 'points'];
    }
}
