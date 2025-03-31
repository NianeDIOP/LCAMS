<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonneeDetaillee extends Model
{
    use HasFactory;

    protected $table = 'donnees_detaillees';
    
    protected $fillable = [
        'eleve_id',
        'discipline_id',
        'valeur',
        'type', // 'moy_dd', 'comp_d', 'moy_d', 'rang_d'
        'annee_scolaire_id',
        'semestre'
    ];

    public function eleve()
    {
        return $this->belongsTo(Eleve::class);
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class);
    }

    public function anneeScolaire()
    {
        return $this->belongsTo(AnneeScolaire::class);
    }
}