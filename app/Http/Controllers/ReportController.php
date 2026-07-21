<?php

namespace App\Http\Controllers;

use App\Exports\PredictionsExport;
use App\Models\Student;
use App\Models\StudentPrediction;
use App\Models\StudentViolation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->has('start_date') ? (string) $request->string('start_date') : now()->toDateString();
        $endDate = $request->has('end_date') ? (string) $request->string('end_date') : now()->toDateString();

        $topRisk = StudentPrediction::query()
            ->with(['student', 'suggestedViolation'])
            ->whereHas('student.studentViolations', function ($query) use ($startDate, $endDate) {
                $query->when($startDate !== '', fn ($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate !== '', fn ($q) => $q->whereDate('created_at', '<=', $endDate));
            })
            ->orderByDesc('rank_score')
            ->limit(10)
            ->get();

        return view('reports.index', [
            'topRisk' => $topRisk,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function pdf(Request $request)
    {
        $startDate = $request->has('start_date') ? (string) $request->string('start_date') : now()->toDateString();
        $endDate = $request->has('end_date') ? (string) $request->string('end_date') : now()->toDateString();

        $summary = [
            'total_students' => Student::query()->count(),
            'total_violations' => StudentViolation::query()
                ->when($startDate !== '', fn ($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate !== '', fn ($query) => $query->whereDate('created_at', '<=', $endDate))
                ->count(),
            'total_predictions' => StudentPrediction::query()
                ->whereHas('student.studentViolations', function ($query) use ($startDate, $endDate) {
                    $query->when($startDate !== '', fn ($q) => $q->whereDate('created_at', '>=', $startDate))
                        ->when($endDate !== '', fn ($q) => $q->whereDate('created_at', '<=', $endDate));
                })
                ->count(),
            'high_risk_students' => StudentPrediction::query()
                ->whereHas('student.studentViolations', function ($query) use ($startDate, $endDate) {
                    $query->when($startDate !== '', fn ($q) => $q->whereDate('created_at', '>=', $startDate))
                        ->when($endDate !== '', fn ($q) => $q->whereDate('created_at', '<=', $endDate));
                })
                ->where('predicted_to_reoffend', true)->count(),
        ];

        $topRisk = StudentPrediction::query()
            ->with('student')
            ->whereHas('student.studentViolations', function ($query) use ($startDate, $endDate) {
                $query->when($startDate !== '', fn ($q) => $q->whereDate('created_at', '>=', $startDate))
                    ->when($endDate !== '', fn ($q) => $q->whereDate('created_at', '<=', $endDate));
            })
            ->orderByDesc('rank_score')
            ->limit(20)
            ->get();

        return Pdf::loadView('reports.pdf', [
            'summary' => $summary,
            'topRisk' => $topRisk,
        ])->download('laporan-sipesa.pdf');
    }

    public function excel(Request $request)
    {
        $startDate = $request->has('start_date') ? (string) $request->string('start_date') : now()->toDateString();
        $endDate = $request->has('end_date') ? (string) $request->string('end_date') : now()->toDateString();

        return Excel::download(new PredictionsExport($startDate, $endDate), 'laporan-sipesa.xlsx');
    }
}
