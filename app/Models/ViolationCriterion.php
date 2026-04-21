<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ViolationCriterion extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'weight',
    ];

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class);
    }
}
