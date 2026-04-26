<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\StudentViolation;
use App\Models\Violation;
use App\Models\ViolationCriterion;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'students' => Student::query()->count(),
            'criteria' => ViolationCriterion::query()->count(),
            'violations' => Violation::query()->count(),
            'student_violations' => StudentViolation::query()->count(),
            'predictions' => StudentPrediction::query()->count(),
            'high_risk' => StudentPrediction::query()->where('predicted_to_reoffend', true)->count(),
        ];

        $safeCount = max(0, $stats['predictions'] - $stats['high_risk']);
        $safePercent = $stats['predictions'] > 0
            ? (int) round(($safeCount / $stats['predictions']) * 100)
            : 0;
        $riskPercent = 100 - $safePercent;

        $topRankings = StudentPrediction::query()
            ->with(['student', 'suggestedViolation'])
            ->orderByDesc('rank_score')
            ->limit(10)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'safePercent' => $safePercent,
            'riskPercent' => $riskPercent,
            'topRankings' => $topRankings,
        ]);
    }
}
