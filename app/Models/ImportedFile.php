<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExcelData extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_id',
        'row_number',
        'data',
    ];

    /**
     * Relation avec le fichier importé
     */
    public function importedFile()
    {
        return $this->belongsTo(ImportedFile::class, 'file_id');
    }

    /**
     * Récupère les données décodées du JSON
     */
    public function getDecodedDataAttribute()
    {
        return json_decode($this->data);
    }
}