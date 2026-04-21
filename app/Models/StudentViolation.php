<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentViolation extends Model
{
    protected $fillable = [
        'student_id',
        'violation_id',
        'occurred_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'occurred_at' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class);
    }
}
