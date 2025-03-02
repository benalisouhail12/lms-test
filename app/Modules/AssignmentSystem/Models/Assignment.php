<?php

namespace App\Modules\AssignmentSystem\Models;


use App\Modules\CourseManagement\Models\Activity;
use App\Modules\CourseManagement\Models\Course;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'instructions', 'course_id', 'activity_id',
        'due_date', 'available_from', 'max_points', 'passing_grade',
        'allow_late_submissions', 'late_submission_penalty',
        'enable_plagiarism_detection', 'allowed_file_types',
        'max_file_size', 'max_attempts', 'status', 'is_group_assignment'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'available_from' => 'datetime',
        'allowed_file_types' => 'array',
        'enable_plagiarism_detection' => 'boolean',
        'allow_late_submissions' => 'boolean',
        'is_group_assignment' => 'boolean',
    ];

    // Relations
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function versions()
    {
        return $this->hasMany(AssignmentVersion::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function criteria()
    {
        return $this->hasMany(GradingCriteria::class);
    }

    public function groups()
    {
        return $this->hasMany(AssignmentGroup::class);
    }

    public function extensions()
    {
        return $this->hasMany(AssignmentExtension::class);
    }
}




















