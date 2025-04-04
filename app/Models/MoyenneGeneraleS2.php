<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoyenneGeneraleS2 extends Model
{
    use HasFactory;

    protected $table = 'moyennes_generales_s2';

    protected $fillable = [
        'eleve_id',
        'moyenne',
        'rang',
        'retard',
        'absence',
        'conseil_discipline',
        'appreciation',
        'decision',
        'observation',
        'annee_scolaire_id',
    ];

    protected $casts = [
        'moyenne' => 'float',
        'rang' => 'integer',
    ];

    // Relations
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}