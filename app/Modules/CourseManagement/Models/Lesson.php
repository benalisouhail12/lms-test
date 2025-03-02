<?php
namespace App\Modules\CourseManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
class Lesson extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'title',
        'description',
        'content',
        'position',
        'section_id',
        'estimated_duration',
        'is_published',
        'lesson_type', // VIDEO, TEXT, QUIZ, ASSIGNMENT, etc.
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'estimated_duration' => 'integer', // in minutes
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('lesson_media')
            ->singleFile();

        $this->addMediaCollection('attachments');
    }
}
