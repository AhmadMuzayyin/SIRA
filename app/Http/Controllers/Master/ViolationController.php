<?php

namespace App\Http\Controllers\Master;

use App\Exports\ViolationsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ViolationRequest;
use App\Imports\ViolationsImport;
use App\Models\Violation;
use App\Models\ViolationCriterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ViolationController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->string('q'));

        $violations = Violation::query()
            ->with('criterion')
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery
                        ->where('code', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('master.violations.index', [
            'violations' => $violations,
            'criteria' => ViolationCriterion::query()->orderBy('code')->get(),
            'keyword' => $keyword,
        ]);
    }

    public function store(ViolationRequest $request): RedirectResponse
    {
        Violation::query()->create($request->validated());

        return back()->with('status', 'Master pelanggaran berhasil ditambahkan.');
    }

    public function update(ViolationRequest $request, Violation $violation): RedirectResponse
    {
        $violation->update($request->validated());

        return back()->with('status', 'Master pelanggaran berhasil diperbarui.');
    }

    public function destroy(Violation $violation): RedirectResponse
    {
        $violation->delete();

        return back()->with('status', 'Master pelanggaran berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new ViolationsImport, $request->file('file'));

        return back()->with('status', 'Import master pelanggaran selesai.');
    }

    public function export()
    {
        return Excel::download(new ViolationsExport, 'violations.xlsx');
    }
}
