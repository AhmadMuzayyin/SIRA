<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ViolationCriterionRequest;
use App\Imports\ViolationCriteriaImport;
use App\Models\ViolationCriterion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ViolationCriterionController extends Controller
{
    public function index(Request $request): View
    {
        $keyword = trim((string) $request->string('q'));

        $criteria = ViolationCriterion::query()
            ->when($keyword !== '', function ($query) use ($keyword) {
                $query->where(function ($innerQuery) use ($keyword) {
                    $innerQuery
                        ->where('code', 'like', "%{$keyword}%")
                        ->orWhere('name', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%");
                });
            })
            ->orderBy('code')
            ->paginate(10)
            ->withQueryString();

        return view('master.violation-criteria.index', [
            'criteria' => $criteria,
            'keyword' => $keyword,
        ]);
    }

    public function store(ViolationCriterionRequest $request): RedirectResponse
    {
        ViolationCriterion::query()->create($request->validated());

        return back()->with('status', 'Kriteria pelanggaran berhasil ditambahkan.');
    }

    public function update(ViolationCriterionRequest $request, ViolationCriterion $violationCriterion): RedirectResponse
    {
        $violationCriterion->update($request->validated());

        return back()->with('status', 'Kriteria pelanggaran berhasil diperbarui.');
    }

    public function destroy(ViolationCriterion $violationCriterion): RedirectResponse
    {
        $violationCriterion->delete();

        return back()->with('status', 'Kriteria pelanggaran berhasil dihapus.');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
        ]);

        Excel::import(new ViolationCriteriaImport, $request->file('file'));

        return back()->with('status', 'Import kriteria pelanggaran selesai.');
    }
}
