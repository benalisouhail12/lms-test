<?php

namespace App\Modules\AssignmentSystem\Models;

use App\Modules\Authentication\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_submission_id', 'points_earned', 'points_possible',
        'percentage', 'letter_grade', 'graded_by', 'graded_at', 'is_final'
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'is_final' => 'boolean',
    ];

    public function submission()
    {
        return $this->belongsTo(AssignmentSubmission::class, 'assignment_submission_id');
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    public function criteriaGrades()
    {
        return $this->hasMany(CriteriaGrade::class);
    }
}
