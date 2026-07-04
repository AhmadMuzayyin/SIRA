<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    public const LEMBAGAS = [
        'L01' => 'Bahasa Inggris',
        'L02' => 'Bahasa Arab',
        'L03' => 'DKL',
        'L04' => 'JQL',
        'L05' => 'MTQ',
    ];

    public const ROOMS = [
        'ING-1' => 'Bahasa Inggris 1',
        'ING-2' => 'Bahasa Inggris 2',
        'ING-3' => 'Bahasa Inggris 3',
        'ING-4' => 'Bahasa Inggris 4',
        'ARB-1' => 'Bahasa Arab 1',
        'ARB-2' => 'Bahasa Arab 2',
        'ARB-3' => 'Bahasa Arab 3',
        'ARB-4' => 'Bahasa Arab 4',
        'ARB-5' => 'Bahasa Arab 5',
        'DKL-1' => 'DKL 1',
        'DKL-2' => 'DKL 2',
        'DKL-3' => 'DKL 3',
        'DKL-4' => 'DKL 4',
        'DKL-5' => 'DKL 5',
        'DKL-6' => 'DKL 6',
        'JQL-1' => 'JQL 1',
        'MTQ-1' => 'MTQ 1',
        'MTQ-2' => 'MTQ 2',
        'MTQ-3' => 'MTQ 3',
        'MTQ-4' => 'MTQ 4',
        'MTQ-5' => 'MTQ 5',
        'MTQ-6' => 'MTQ 6',
        'MTQ-7' => 'MTQ 7',
        'MTQ-8' => 'MTQ 8',
        'MTQ-9' => 'MTQ 9',
    ];

    protected $fillable = [
        'nis',
        'name',
        'gender',
        'room',
        'lembaga',
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

    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }
}
