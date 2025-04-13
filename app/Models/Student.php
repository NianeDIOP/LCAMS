<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'matricule',
        'classroom_id',
        'sexe',
    ];

    /**
     * Relation avec la classe de l'élève
     */
    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    /**
     * Relation avec la moyenne du semestre 1
     */
    public function semester1Average(): HasOne
    {
        return $this->hasOne(Semester1Average::class);
    }

    /**
     * Relation avec les notes des disciplines du semestre 1
     */
    public function semester1SubjectMarks(): HasMany
    {
        return $this->hasMany(Semester1SubjectMark::class);
    }

    /**
     * Retourne le nom complet de l'élève
     */
    public function getFullNameAttribute(): string
    {
        return $this->nom . ' ' . $this->prenom;
    }
}