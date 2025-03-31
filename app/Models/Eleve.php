<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Eleve extends Model
{
    use HasFactory;

    protected $fillable = [
        'ien',
        'prenom',
        'nom',
        'sexe',
        'date_naissance',
        'lieu_naissance',
        'classe_id',
        'annee_scolaire_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
    ];

    // Relations
    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }

    public function notesS1(): HasMany
    {
        return $this->hasMany(NoteS1::class);
    }

    public function notesS2(): HasMany
    {
        return $this->hasMany(NoteS2::class);
    }

    public function moyenneGeneraleS1(): HasOne
    {
        return $this->hasOne(MoyenneGeneraleS1::class);
    }

    public function moyenneGeneraleS2(): HasOne
    {
        return $this->hasOne(MoyenneGeneraleS2::class);
    }

    public function decisionFinale(): HasOne
    {
        return $this->hasOne(DecisionFinale::class);
    }
}