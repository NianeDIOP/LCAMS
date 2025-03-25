<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_fichier',
        'chemin',
        'semestre',
        'type',
        'niveau_id',
        'classe_id',
        'nombre_lignes',
    ];

    /**
     * Relation avec le niveau
     */
    public function niveau()
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Relation avec la classe
     */
    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    /**
     * Relation avec les données Excel
     */
    public function excelData()
    {
        return $this->hasMany(ExcelData::class, 'file_id');
    }
}