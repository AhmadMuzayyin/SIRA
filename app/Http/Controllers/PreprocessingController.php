<?php

namespace App\Http\Controllers;

use App\Models\StudentPreprocessing;
use App\Services\StudentPreprocessingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PreprocessingController extends Controller
{
    public function index(): View
    {
        $preprocessings = StudentPreprocessing::query()
            ->with('student')
            ->orderByDesc('processed_at')
            ->paginate(10);

        return view('preprocessing.index', [
            'preprocessings' => $preprocessings,
        ]);
    }

    public function run(StudentPreprocessingService $preprocessingService): RedirectResponse
    {
        $preprocessingService->processAll();

        return back()->with('status', 'Proses preprocessing selesai dijalankan.');
    }
}
