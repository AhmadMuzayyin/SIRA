<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPrediction extends Model
{
    protected $fillable = [
        'student_id',
        'risk_probability',
        'predicted_to_reoffend',
        'rank_score',
        'suggested_violation_id',
        'evidence',
        'predicted_at',
    ];

    protected function casts(): array
    {
        return [
            'predicted_to_reoffend' => 'boolean',
            'risk_probability' => 'decimal:4',
            'evidence' => 'array',
            'predicted_at' => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function suggestedViolation(): BelongsTo
    {
        return $this->belongsTo(Violation::class, 'suggested_violation_id');
    }
}
