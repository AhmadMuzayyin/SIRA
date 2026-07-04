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
        'L05' => 'NTQ',
    ];

    public const ROOMS = [
        'A1' => 'Kamar A1',
        'A2' => 'Kamar A2',
        'A3' => 'Kamar A3',
        'A4' => 'Kamar A4',
        'A5' => 'Kamar A5',
        'A6' => 'Kamar A6',
        'A7' => 'Kamar A7',
        'A8' => 'Kamar A8',
        'A9' => 'Kamar A9',
        'B1' => 'Kamar B1',
        'B2' => 'Kamar B2',
        'B3' => 'Kamar B3',
        'B4' => 'Kamar B4',
        'B5' => 'Kamar B5',
        'B6' => 'Kamar B6',
        'B7' => 'Kamar B7',
        'B8' => 'Kamar B8',
        'B9' => 'Kamar B9',
        'B10' => 'Kamar B10',
        'C1' => 'Kamar C1',
        'C2' => 'Kamar C2',
        'C3' => 'Kamar C3',
        'C4' => 'Kamar C4',
        'C5' => 'Kamar C5',
        'C6' => 'Kamar C6',
        'C7' => 'Kamar C7',
        'C8' => 'Kamar C8',
        'C9' => 'Kamar C9',
        'D1' => 'Kamar D1',
        'D2' => 'Kamar D2',
        'D3' => 'Kamar D3',
        'D4' => 'Kamar D4',
        'D5' => 'Kamar D5',
        'D6' => 'Kamar D6',
        'D7' => 'Kamar D7',
        'D8' => 'Kamar D8',
        'D9' => 'Kamar D9',
        'E1' => 'Kamar E1',
        'E2' => 'Kamar E2',
        'E3' => 'Kamar E3',
        'E4' => 'Kamar E4',
        'E5' => 'Kamar E5',
        'E6' => 'Kamar E6',
        'E7' => 'Kamar E7',
        'E8' => 'Kamar E8',
        'E9' => 'Kamar E9',
        'E10' => 'Kamar E10',
        'E11' => 'Kamar E11',
        'E12' => 'Kamar E12',
        'F1' => 'Kamar F1',
        'F2' => 'Kamar F2',
        'F3' => 'Kamar F3',
        'F4' => 'Kamar F4',
        'F5' => 'Kamar F5',
        'F6' => 'Kamar F6',
        'F7' => 'Kamar F7',
        'F8' => 'Kamar F8',
        'F9' => 'Kamar F9',
        'F10' => 'Kamar F10',
        'F11' => 'Kamar F11',
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
