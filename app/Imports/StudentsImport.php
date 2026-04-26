<?php

namespace App\Imports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToModel, WithHeadingRow
{
    public function model(array $row): Student
    {
        return Student::query()->updateOrCreate(
            ['nis' => trim((string) ($row['nis'] ?? ''))],
            [
                'name' => trim((string) ($row['name'] ?? '')),
                'gender' => $this->nullableString($row['gender'] ?? null),
                'room' => $this->nullableString($row['room'] ?? null),
                'status' => in_array($row['status'] ?? 'aktif', ['aktif', 'nonaktif'], true)
                    ? $row['status']
                    : 'aktif',
            ],
        );
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = trim((string) $value);

        return $normalized === '' ? null : $normalized;
    }
}
