<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnneeScolaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'date_debut',
        'date_fin',
        'active',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'active' => 'boolean',
    ];

    // Relations
    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }

    public function notesS1(): HasMany
    {
        return $this->hasMany(NoteS1::class);
    }

    public function notesS2(): HasMany
    {
        return $this->hasMany(NoteS2::class);
    }

    public function moyennesGeneralesS1(): HasMany
    {
        return $this->hasMany(MoyenneGeneraleS1::class);
    }

    public function moyennesGeneralesS2(): HasMany
    {
        return $this->hasMany(MoyenneGeneraleS2::class);
    }

    public function decisionsFinales(): HasMany
    {
        return $this->hasMany(DecisionFinale::class);
    }
}