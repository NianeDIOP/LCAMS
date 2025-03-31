<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom_etablissement',
        'adresse',
        'telephone',
        'inspection_academie',
        'inspection_education_formation',
        'logo_path',
    ];
}