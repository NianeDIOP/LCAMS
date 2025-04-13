<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester1SubjectMark extends Model
{
    protected $fillable = [
        'student_id',
        'subject_id',
        'note',
        'rang',
        'appreciation',
    ];

    /**
     * Relation avec l'élève
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Relation avec la discipline
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}