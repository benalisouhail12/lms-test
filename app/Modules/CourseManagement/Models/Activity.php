<?php

namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'activity_type', // QUIZ, ASSIGNMENT, DISCUSSION, etc.
        'lesson_id',
        'is_required',
        'points',
        'due_date',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'points' => 'integer',
        'due_date' => 'datetime',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}
