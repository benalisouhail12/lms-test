<?php

namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Modules\Authentication\Models\User;
use App\Modules\CourseManagement\Enums\CourseType;
use App\Modules\CourseManagement\Enums\CourseStatus;
use App\Modules\CourseManagement\Models\Department;
use App\Modules\CourseManagement\Models\Program;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Course extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'short_description',
        'learning_objectives',
        'course_type', // ONLINE, HYBRID, IN_PERSON
        'status', // DRAFT, PUBLISHED, ARCHIVED
        'start_date',
        'end_date',
        'level',
        'duration_in_weeks',
        'credit_hours',
        'capacity',
        'department_id',
        'program_id',
    ];

    protected $casts = [
        'course_type' => CourseType::class,
        'status' => CourseStatus::class,
        'learning_objectives' => 'array',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('position');
    }

    public function instructors()
    {
        return $this->belongsToMany(User::class, 'course_instructors')
            ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot('status', 'enrolled_at', 'completed_at', 'progress_percentage')
            ->withTimestamps();
    }

    public function learningPaths()
    {
        return $this->belongsToMany(LearningPath::class)
            ->withPivot('position')
            ->withTimestamps();
    }

    public function prerequisites()
    {
        return $this->belongsToMany(
            Course::class,
            'course_prerequisites',
            'course_id',
            'prerequisite_course_id'
        )->withTimestamps();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('course_thumbnail')
            ->singleFile();

        $this->addMediaCollection('course_banner')
            ->singleFile();

        $this->addMediaCollection('course_materials');
    }
}











