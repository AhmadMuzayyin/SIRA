<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Violation extends Model
{
    protected $fillable = [
        'violation_criterion_id',
        'code',
        'name',
        'points',
    ];

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(ViolationCriterion::class, 'violation_criterion_id');
    }

    public function studentViolations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }
}
