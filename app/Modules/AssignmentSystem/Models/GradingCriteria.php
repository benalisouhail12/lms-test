<?php

namespace App\Modules\AssignmentSystem\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradingCriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id', 'title', 'description', 'max_points', 'weight'
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function criteriaGrades()
    {
        return $this->hasMany(CriteriaGrade::class);
    }
}
