<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoteS1 extends Model
{
    use HasFactory;

    protected $table = 'notes_s1';

    protected $fillable = [
        'eleve_id',
        'discipline_id',
        'moy_dd',
        'comp_d',
        'moy_d',
        'rang_d',
        'annee_scolaire_id',
    ];

    protected $casts = [
        'moy_dd' => 'float',
        'comp_d' => 'float',
        'moy_d' => 'float',
        'rang_d' => 'integer',
    ];

    // Relations
    public function eleve(): BelongsTo
    {
        return $this->belongsTo(Eleve::class);
    }

    public function discipline(): BelongsTo
    {
        return $this->belongsTo(Discipline::class);
    }

    public function anneeScolaire(): BelongsTo
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}