<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    use HasFactory;

    protected $fillable = [
        'niveau_id',
        'libelle',
        'code',
        'effectif',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // Relations
    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    public function eleves(): HasMany
    {
        return $this->hasMany(Eleve::class);
    }
}