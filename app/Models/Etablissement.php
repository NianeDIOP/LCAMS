<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Etablissement extends Model
{
    use HasFactory;
    
    protected $table = 'etablissements';
    
    protected $fillable = [
        'nom',
        'adresse',
        'telephone',
        'email',
        'logo',
        'annee_scolaire',
        'academie',
        'ief',
    ];
}