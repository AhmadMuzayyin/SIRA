<?php

namespace App\Imports;

use App\Models\ViolationCriterion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ViolationCriteriaImport implements ToModel, WithHeadingRow
{
    public function model(array $row): ViolationCriterion
    {
        $category = strtolower(trim((string) ($row['category'] ?? 'ringan')));

        if (! in_array($category, ['ringan', 'sedang', 'berat'], true)) {
            $category = 'ringan';
        }

        return ViolationCriterion::query()->updateOrCreate(
            ['code' => trim((string) ($row['code'] ?? ''))],
            [
                'name' => trim((string) ($row['name'] ?? '')),
                'category' => $category,
                'weight' => max(1, (int) ($row['weight'] ?? 1)),
            ],
        );
    }
}
