<?php

namespace Database\Seeders;

use App\Models\Student;
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

        for ($index = 1; $index <= 50; $index++) {
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

        $violationIds = Violation::query()->pluck('id')->values();
        $baseDate = Carbon::parse('2026-01-01');

        for ($index = 0; $index < 50; $index++) {
            $student = $students[$index];
            $violationId = $violationIds[$index % $violationIds->count()];
            $occurredAt = $baseDate->copy()->addDays($index);

            StudentViolation::query()->updateOrCreate(
                [
                    'student_id' => $student->id,
                    'violation_id' => $violationId,
                    'occurred_at' => $occurredAt->toDateString(),
                ],
                [
                    'notes' => 'Data uji seeder #'.($index + 1),
                ],
            );
        }

        app(StudentPreprocessingService::class)->processAll();
        app(NaiveBayesPredictionService::class)->predictAll();
    }
}
