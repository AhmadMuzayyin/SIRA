<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\Violation;
use Illuminate\Support\Collection;

class NaiveBayesPredictionService
{
    public function __construct(
        private readonly StudentPreprocessingService $preprocessingService,
    ) {}

    public function predictAll(): void
    {
        Student::query()
            ->active()
            ->with(['preprocessing', 'studentViolations', 'prediction'])
            ->chunkById(100, function (Collection $students): void {
                foreach ($students as $student) {
                    $this->predictStudent($student);
                }
            });
    }

    public function predictStudent(Student $student): StudentPrediction
    {
        $preprocessing = $student->preprocessing ?? $this->preprocessingService->processStudent($student);

        $weightedScore =
            ($preprocessing->total_violations * 0.16) +
            ($preprocessing->ringan_count * 0.08) +
            ($preprocessing->sedang_count * 0.18) +
            ($preprocessing->berat_count * 0.26) +
            ($preprocessing->jamaah_absence_count * 0.12);

        $adjustedScore = $weightedScore - 1.25;
        $riskProbability = 1 / (1 + exp(-$adjustedScore));
        $predictedToReoffend = $riskProbability >= 0.5;
        $rankScore = (int) round($riskProbability * 1000);

        $suggestedViolationId = $student->studentViolations()
            ->select('violation_id')
            ->selectRaw('count(*) as violation_count')
            ->groupBy('violation_id')
            ->orderByDesc('violation_count')
            ->value('violation_id');

        if ($suggestedViolationId === null) {
            $suggestedViolationId = Violation::query()->orderByDesc('points')->value('id');
        }

        return StudentPrediction::query()->updateOrCreate(
            ['student_id' => $student->id],
            [
                'risk_probability' => round($riskProbability, 4),
                'predicted_to_reoffend' => $predictedToReoffend,
                'rank_score' => $rankScore,
                'suggested_violation_id' => $suggestedViolationId,
                'evidence' => [
                    'features' => $preprocessing->feature_vector,
                    'weighted_score' => round($weightedScore, 6),
                    'adjusted_score' => round($adjustedScore, 6),
                ],
                'predicted_at' => now(),
            ],
        );
    }
}
