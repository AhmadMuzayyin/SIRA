<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentPreprocessing;
use App\Models\StudentViolation;
use Illuminate\Support\Collection;

class StudentPreprocessingService
{
    public function processAll(): void
    {
        Student::query()
            ->active()
            ->with(['studentViolations.violation.criterion'])
            ->chunkById(100, function (Collection $students): void {
                foreach ($students as $student) {
                    $this->processStudent($student);
                }
            });
    }

    public function processStudent(Student $student): StudentPreprocessing
    {
        $violations = StudentViolation::query()
            ->where('student_id', $student->id)
            ->with('violation.criterion')
            ->orderByDesc('occurred_at')
            ->get();

        $totalViolations = $violations->count();
        $ringanCount = $this->countByCategory($violations, 'ringan');
        $sedangCount = $this->countByCategory($violations, 'sedang');
        $beratCount = $this->countByCategory($violations, 'berat');
        $jamaahAbsenceCount = $violations->filter(function (StudentViolation $item): bool {
            return str_contains(strtolower($item->violation->name ?? ''), 'jamaah');
        })->count();

        $featureVector = [
            'total_bin' => $this->binValue($totalViolations, [0, 2, 5]),
            'ringan_bin' => $this->binValue($ringanCount, [0, 2, 4]),
            'sedang_bin' => $this->binValue($sedangCount, [0, 1, 3]),
            'berat_bin' => $this->binValue($beratCount, [0, 1, 2]),
            'jamaah_absence_bin' => $this->binValue($jamaahAbsenceCount, [0, 2, 4]),
        ];

        return StudentPreprocessing::query()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'total_violations' => $totalViolations,
                'ringan_count' => $ringanCount,
                'sedang_count' => $sedangCount,
                'berat_count' => $beratCount,
                'jamaah_absence_count' => $jamaahAbsenceCount,
                'last_violation_at' => $violations->first()?->occurred_at,
                'feature_vector' => $featureVector,
                'processed_at' => now(),
            ],
        );
    }

    private function countByCategory(Collection $violations, string $category): int
    {
        return $violations->filter(function (StudentViolation $item) use ($category): bool {
            return $item->violation?->criterion?->category === $category;
        })->count();
    }

    private function binValue(int $value, array $threshold): string
    {
        if ($value <= $threshold[0]) {
            return 'none';
        }

        if ($value <= $threshold[1]) {
            return 'low';
        }

        if ($value <= $threshold[2]) {
            return 'medium';
        }

        return 'high';
    }
}
