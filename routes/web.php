<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\StudentController;
use App\Http\Controllers\Master\ViolationController;
use App\Http\Controllers\Master\ViolationCriterionController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\PreprocessingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentViolationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('students', StudentController::class)->except(['create', 'show', 'edit']);
    Route::patch('students/{student}/status', [StudentController::class, 'updateStatus'])->name('students.update-status');
    Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('students/export', [StudentController::class, 'export'])->name('students.export');

    Route::resource('violation-criteria', ViolationCriterionController::class)->except(['create', 'show', 'edit']);
    Route::post('violation-criteria/import', [ViolationCriterionController::class, 'import'])->name('violation-criteria.import');

    Route::resource('violations', ViolationController::class)->except(['create', 'show', 'edit']);
    Route::post('violations/import', [ViolationController::class, 'import'])->name('violations.import');
    Route::get('violations/export', [ViolationController::class, 'export'])->name('violations.export');

    Route::resource('student-violations', StudentViolationController::class)->except(['create', 'show', 'edit']);
    Route::get('student-violations/export', [StudentViolationController::class, 'export'])->name('student-violations.export');

    Route::get('preprocessing', [PreprocessingController::class, 'index'])->name('preprocessing.index');
    Route::post('preprocessing/run', [PreprocessingController::class, 'run'])->name('preprocessing.run');

    Route::get('predictions', [PredictionController::class, 'index'])->name('predictions.index');
    Route::post('predictions/run', [PredictionController::class, 'run'])->name('predictions.run');
    Route::get('predictions/export', [PredictionController::class, 'export'])->name('predictions.export');

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/pdf', [ReportController::class, 'pdf'])->name('reports.pdf');
    Route::get('reports/excel', [ReportController::class, 'excel'])->name('reports.excel');
});

require __DIR__.'/settings.php';
