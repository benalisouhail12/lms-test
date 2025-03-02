<?php

namespace App\Modules\AssignmentSystem\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CriteriaGrade extends Model
{
    use HasFactory;

    protected $fillable = [
        'grade_id', 'grading_criteria_id', 'points_earned', 'comment'
    ];

    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

    public function criteria()
    {
        return $this->belongsTo(GradingCriteria::class, 'grading_criteria_id');
    }
}
