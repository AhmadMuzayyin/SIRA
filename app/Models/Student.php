<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'gender',
        'room',
        'status',
    ];

    public function studentViolations(): HasMany
    {
        return $this->hasMany(StudentViolation::class);
    }

    public function preprocessing(): HasOne
    {
        return $this->hasOne(StudentPreprocessing::class);
    }

    public function prediction(): HasOne
    {
        return $this->hasOne(StudentPrediction::class);
    }
}
