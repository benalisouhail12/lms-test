<?php

namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LearningPath extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'status',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class)
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('pivot_position');
    }
}
