<?php

namespace App\Http\Controllers;

use App\Exports\PredictionsExport;
use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\StudentViolation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(): View
    {
        $topRisk = StudentPrediction::query()
            ->with(['student', 'suggestedViolation'])
            ->orderByDesc('rank_score')
            ->limit(10)
            ->get();

        return view('reports.index', [
            'topRisk' => $topRisk,
        ]);
    }

    public function pdf()
    {
        $summary = [
            'total_students' => Student::query()->count(),
            'total_violations' => StudentViolation::query()->count(),
            'total_predictions' => StudentPrediction::query()->count(),
            'high_risk_students' => StudentPrediction::query()->where('predicted_to_reoffend', true)->count(),
        ];

        $topRisk = StudentPrediction::query()
            ->with('student')
            ->orderByDesc('rank_score')
            ->limit(20)
            ->get();

        return Pdf::loadView('reports.pdf', [
            'summary' => $summary,
            'topRisk' => $topRisk,
        ])->download('laporan-sira.pdf');
    }

    public function excel()
    {
        return Excel::download(new PredictionsExport, 'laporan-prediksi.xlsx');
    }
}
