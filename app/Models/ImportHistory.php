<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    use HasFactory;

    /**
     * Les attributs qui peuvent être assignés en masse
     *
     * @var array
     */
    protected $fillable = [
        'grade_level_id',
        'user_id',
        'file_path',
        'status',
        'details'
    ];

    /**
     * Les attributs à caster
     *
     * @var array
     */
    protected $casts = [
        'details' => 'array',
    ];

    /**
     * Récupérer le niveau scolaire associé
     */
    public function gradeLevel()
    {
        return $this->belongsTo(GradeLevel::class);
    }

    /**
     * Récupérer l'utilisateur qui a effectué l'importation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}