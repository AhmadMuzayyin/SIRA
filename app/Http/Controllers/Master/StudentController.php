<?php

namespace App\Http\Controllers\Master;

use App\Exports\StudentsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Imports\StudentsImport;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class StudentController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->string('q'));

        $students = Student::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery
                        ->where('nis', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('room', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $rooms = Student::ROOMS;
        $lembagas = Student::LEMBAGAS;

        return view('master.students.index', [
            'students' => $students,
            'keyword' => $keyword,
            'rooms' => $rooms,
            'lembagas' => $lembagas,
        ]);
    }

    public function store(StudentRequest $request): RedirectResponse
    {
        Student::query()->create($request->validated());

        return back()->with('status', 'Data santri berhasil ditambahkan.');
    }

    public function update(StudentRequest $request, Student $student): RedirectResponse
    {
        $student->update($request->validated());

        return back()->with('status', 'Data santri berhasil diperbarui.');
    }

    public function updateStatus(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
        ]);

        $student->update($validated);

        return back()->with('status', 'Status santri berhasil diperbarui.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return back()->with('status', 'Data santri berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return back()->with('status', 'Import data santri selesai.');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }
}
