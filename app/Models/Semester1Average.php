<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester1Average extends Model
{
    protected $fillable = [
        'student_id',
        'moyenne',
        'rang',
        'decision',
        'appreciation',
        'observations'
    ];

    protected $casts = [
        'moyenne' => 'float',
        'rang' => 'integer',
    ];

    /**
     * Relation avec l'élève
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}