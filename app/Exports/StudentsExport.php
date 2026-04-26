<?php

namespace App\Exports;

use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsExport implements FromCollection, WithHeadings
{
    public function collection(): Collection
    {
        return Student::query()
            ->orderBy('name')
            ->get(['nis', 'name', 'gender', 'room', 'status', 'created_at']);
    }

    public function headings(): array
    {
        return ['nis', 'name', 'gender', 'room', 'status', 'created_at'];
    }
}
