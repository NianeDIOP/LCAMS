<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Classe extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nom',
        'niveau_id',
        'annee_scolaire',
        'effectif_total',
        'effectif_garcons',
        'effectif_filles',
        'active',
    ];
    
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }
}