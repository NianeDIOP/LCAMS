<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportHistory extends Model
{
    use HasFactory;

    protected $table = 'import_history';

    protected $fillable = [
        'niveau_id',
        'classe_id',
        'annee_scolaire_id',
        'fichier_original',
        'fichier_stocke',
        'semestre',
        'nb_eleves',
        'nb_disciplines',
        'resume',
        'statut',
    ];

    protected $casts = [
        'resume' => 'array',
    ];

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}