<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\StudentPreprocessing;
use App\Models\StudentViolation;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationCriterion;
use App\Services\NaiveBayesPredictionService;
use App\Services\StudentPreprocessingService;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->firstOrNew([
            'email' => 'admin@admin.com',
        ]);

        $admin->name = 'Administrator';
        $admin->password = 'password';
        $admin->forceFill([
            'email_verified_at' => now(),
        ])->save();

        $criteria = collect([
            ['code' => 'KR', 'name' => 'Kriteria Ringan', 'category' => 'ringan', 'weight' => 1],
            ['code' => 'KS', 'name' => 'Kriteria Sedang', 'category' => 'sedang', 'weight' => 2],
            ['code' => 'KB', 'name' => 'Kriteria Berat', 'category' => 'berat', 'weight' => 3],
        ])->mapWithKeys(function (array $row): array {
            $criterion = ViolationCriterion::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'name' => $row['name'],
                    'category' => $row['category'],
                    'weight' => $row['weight'],
                ],
            );

            return [$row['code'] => $criterion->id];
        });

        $violationRows = [
            ['code' => 'P001', 'name' => 'Terlambat kegiatan pondok', 'criterion' => 'KR', 'points' => 1],
            ['code' => 'P002', 'name' => 'Tidak ikut jamaah shalat wajib', 'criterion' => 'KR', 'points' => 2],
            ['code' => 'P003', 'name' => 'Membolos kegiatan belajar', 'criterion' => 'KS', 'points' => 3],
            ['code' => 'P004', 'name' => 'Keluar area tanpa izin', 'criterion' => 'KS', 'points' => 3],
            ['code' => 'P005', 'name' => 'Merokok di area pondok', 'criterion' => 'KB', 'points' => 5],
            ['code' => 'P006', 'name' => 'Membawa barang terlarang', 'criterion' => 'KB', 'points' => 5],
        ];

        foreach ($violationRows as $row) {
            Violation::query()->updateOrCreate(
                ['code' => $row['code']],
                [
                    'violation_criterion_id' => $criteria[$row['criterion']],
                    'name' => $row['name'],
                    'points' => $row['points'],
                ],
            );
        }

        $students = collect();

        for ($index = 1; $index <= 100; $index++) {
            $nis = sprintf('24%04d', $index);

            $students->push(
                Student::query()->updateOrCreate(
                    ['nis' => $nis],
                    [
                        'name' => fake()->name(),
                        'gender' => fake()->randomElement(['L', 'P']),
                        'room' => fake()->randomElement(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']),
                        'status' => 'aktif',
                    ],
                ),
            );
        }

        $studentIds = $students->pluck('id');

        StudentViolation::query()->whereIn('student_id', $studentIds)->delete();
        StudentPreprocessing::query()->whereIn('student_id', $studentIds)->delete();
        StudentPrediction::query()->whereIn('student_id', $studentIds)->delete();

        $violations = Violation::query()->with('criterion')->get();
        $allViolationIds = $violations->pluck('id')->all();
        $ringanViolationIds = $violations
            ->filter(fn (Violation $violation): bool => $violation->criterion?->category === 'ringan')
            ->pluck('id')
            ->all();
        $sedangViolationIds = $violations
            ->filter(fn (Violation $violation): bool => $violation->criterion?->category === 'sedang')
            ->pluck('id')
            ->all();
        $beratViolationIds = $violations
            ->filter(fn (Violation $violation): bool => $violation->criterion?->category === 'berat')
            ->pluck('id')
            ->all();

        $jamaahViolationId = $violations
            ->first(fn (Violation $violation): bool => str_contains(strtolower($violation->name), 'jamaah'))
            ?->id;

        foreach ($students->values() as $position => $student) {
            $studentNumber = $position + 1;

            if ($studentNumber <= 30) {
                $profile = 'high';
                $eventCount = random_int(6, 10);
            } elseif ($studentNumber <= 65) {
                $profile = 'medium';
                $eventCount = random_int(3, 6);
            } else {
                $profile = 'low';
                $eventCount = random_int(0, 3);
            }

            $baseDate = Carbon::parse('2026-01-01')->addDays($studentNumber * 2);

            for ($eventIndex = 0; $eventIndex < $eventCount; $eventIndex++) {
                $violationId = $this->resolveViolationId(
                    $profile,
                    $ringanViolationIds,
                    $sedangViolationIds,
                    $beratViolationIds,
                    $allViolationIds,
                );

                StudentViolation::query()->create([
                    'student_id' => $student->id,
                    'violation_id' => $violationId,
                    'occurred_at' => $baseDate->copy()->addDays($eventIndex)->toDateString(),
                    'notes' => 'Data uji seeder '.$profile.' #'.$studentNumber.'-'.($eventIndex + 1),
                ]);
            }

            if ($jamaahViolationId !== null) {
                $shouldAddJamaah = ($profile === 'high' && random_int(1, 100) <= 90)
                    || ($profile === 'medium' && random_int(1, 100) <= 45)
                    || ($profile === 'low' && random_int(1, 100) <= 20);

                if ($shouldAddJamaah) {
                    StudentViolation::query()->create([
                        'student_id' => $student->id,
                        'violation_id' => $jamaahViolationId,
                        'occurred_at' => $baseDate->copy()->addDays($eventCount + 1)->toDateString(),
                        'notes' => 'Data uji seeder jamaah #'.$studentNumber,
                    ]);
                }
            }
        }

        app(StudentPreprocessingService::class)->processAll();
        app(NaiveBayesPredictionService::class)->predictAll();
    }

    private function resolveViolationId(
        string $profile,
        array $ringanViolationIds,
        array $sedangViolationIds,
        array $beratViolationIds,
        array $allViolationIds,
    ): int {
        $random = random_int(1, 100);

        if ($profile === 'high') {
            if ($random <= 50) {
                return $this->pickViolationId($beratViolationIds, $allViolationIds);
            }

            if ($random <= 80) {
                return $this->pickViolationId($sedangViolationIds, $allViolationIds);
            }

            return $this->pickViolationId($ringanViolationIds, $allViolationIds);
        }

        if ($profile === 'medium') {
            if ($random <= 20) {
                return $this->pickViolationId($beratViolationIds, $allViolationIds);
            }

            if ($random <= 60) {
                return $this->pickViolationId($sedangViolationIds, $allViolationIds);
            }

            return $this->pickViolationId($ringanViolationIds, $allViolationIds);
        }

        if ($random <= 10) {
            return $this->pickViolationId($sedangViolationIds, $allViolationIds);
        }

        return $this->pickViolationId($ringanViolationIds, $allViolationIds);
    }

    private function pickViolationId(array $candidateViolationIds, array $fallbackViolationIds): int
    {
        $source = $candidateViolationIds !== [] ? $candidateViolationIds : $fallbackViolationIds;

        return (int) $source[array_rand($source)];
    }
}
