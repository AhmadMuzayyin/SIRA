<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPreprocessing extends Model
{
    protected $fillable = [
        'student_id',
        'total_violations',
        'ringan_count',
        'sedang_count',
        'berat_count',
        'jamaah_absence_count',
        'last_violation_at',
        'feature_vector',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'feature_vector' => 'array',
            'processed_at' => 'datetime',
            'last_violation_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
