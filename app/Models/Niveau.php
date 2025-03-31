<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'code',
        'description',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    // Relations
    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }
}