<?php

namespace App\Http\Controllers;

use App\Exports\StudentViolationsExport;
use App\Http\Requests\StudentViolationRequest;
use App\Models\Student;
use App\Models\StudentViolation;
use App\Models\Violation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentViolationController extends Controller
{
    public function index(Request $request): View
    {
        $studentId = $request->integer('student_id');

        $studentViolations = StudentViolation::query()
            ->with(['student', 'violation.criterion'])
            ->when($studentId > 0, fn ($query) => $query->where('student_id', $studentId))
            ->orderByDesc('occurred_at')
            ->paginate(10)
            ->withQueryString();

        return view('student-violations.index', [
            'studentViolations' => $studentViolations,
            'students' => Student::query()->active()->orderBy('name')->get(['id', 'name', 'nis']),
            'violations' => Violation::query()->with('criterion')->orderBy('code')->get(),
            'selectedStudentId' => $studentId,
        ]);
    }

    public function store(StudentViolationRequest $request): RedirectResponse
    {
        StudentViolation::query()->create($request->validated());

        return back()->with('status', 'Pelanggaran santri berhasil dicatat.');
    }

    public function update(StudentViolationRequest $request, StudentViolation $studentViolation): RedirectResponse
    {
        $studentViolation->update($request->validated());

        return back()->with('status', 'Data pelanggaran santri berhasil diperbarui.');
    }

    public function destroy(StudentViolation $studentViolation): RedirectResponse
    {
        $studentViolation->delete();

        return back()->with('status', 'Data pelanggaran santri berhasil dihapus.');
    }

    public function export()
    {
        return Excel::download(new StudentViolationsExport, 'student-violations.xlsx');
    }
}
