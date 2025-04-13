<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'nom',
        'code',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * Relation avec les notes des étudiants dans cette discipline (semestre 1)
     */
    public function semester1Marks(): HasMany
    {
        return $this->hasMany(Semester1SubjectMark::class);
    }
}