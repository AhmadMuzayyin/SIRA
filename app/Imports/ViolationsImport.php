<?php

namespace App\Imports;

use App\Models\Violation;
use App\Models\ViolationCriterion;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ViolationsImport implements ToModel, WithHeadingRow
{
    public function model(array $row): ?Violation
    {
        $criterionCode = trim((string) ($row['criterion_code'] ?? ''));

        $criterionId = ViolationCriterion::query()
            ->where('code', $criterionCode)
            ->value('id');

        if ($criterionId === null) {
            return null;
        }

        return Violation::query()->updateOrCreate(
            ['code' => trim((string) ($row['code'] ?? ''))],
            [
                'violation_criterion_id' => $criterionId,
                'name' => trim((string) ($row['name'] ?? '')),
                'points' => max(1, (int) ($row['points'] ?? 1)),
            ],
        );
    }
}
