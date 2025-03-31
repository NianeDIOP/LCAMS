<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discipline extends Model
{
    use HasFactory;

    protected $fillable = [
        'libelle',
        'code',
        'coefficient',
        'type',
        'discipline_parent_id',
        'actif',
    ];

    protected $casts = [
        'coefficient' => 'float',
        'actif' => 'boolean',
    ];

    // Relations
    public function disciplineParent(): BelongsTo
    {
        return $this->belongsTo(Discipline::class, 'discipline_parent_id');
    }

    public function sousDisciplines(): HasMany
    {
        return $this->hasMany(Discipline::class, 'discipline_parent_id');
    }

    public function notesS1(): HasMany
    {
        return $this->hasMany(NoteS1::class);
    }

    public function notesS2(): HasMany
    {
        return $this->hasMany(NoteS2::class);
    }
}