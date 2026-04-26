<?php

namespace App\Http\Controllers;

use App\Exports\PredictionsExport;
use App\Models\StudentPrediction;
use App\Services\NaiveBayesPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class PredictionController extends Controller
{
    public function index(): View
    {
        $predictions = StudentPrediction::query()
            ->with(['student', 'suggestedViolation'])
            ->orderByDesc('rank_score')
            ->paginate(10);

        return view('predictions.index', [
            'predictions' => $predictions,
        ]);
    }

    public function run(NaiveBayesPredictionService $predictionService): RedirectResponse
    {
        $predictionService->predictAll();

        return back()->with('status', 'Proses prediksi dan ranking selesai dijalankan.');
    }

    public function export()
    {
        return Excel::download(new PredictionsExport, 'predictions.xlsx');
    }
}
