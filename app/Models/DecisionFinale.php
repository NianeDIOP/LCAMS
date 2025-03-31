<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DecisionFinale extends Model
{
    use HasFactory;

    protected $table = 'decisions_finales';

    protected $fillable = [
        'eleve_id',
        'decision',
        'moyenne_annuelle',
        'rang_annuel',
        'annee_scolaire_id',
        'observation',
    ];

    protected $casts = [
        'moyenne_annuelle' => 'float',
        'rang_annuel' => 'integer',
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