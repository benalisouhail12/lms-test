<?php

namespace App\Modules\CourseManagement\Enums;
enum LessonType: string
{
    case TEXT = 'TEXT';
    case VIDEO = 'VIDEO';
    case QUIZ = 'QUIZ';
    case ASSIGNMENT = 'ASSIGNMENT';
    case DISCUSSION = 'DISCUSSION';
    case WEBINAR = 'WEBINAR';

    public function label(): string
    {
        return match($this) {
            self::TEXT => 'Texte',
            self::VIDEO => 'Vidéo',
            self::QUIZ => 'Quiz',
            self::ASSIGNMENT => 'Devoir',
            self::DISCUSSION => 'Discussion',
            self::WEBINAR => 'Webinaire',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::TEXT => 'file-text',
            self::VIDEO => 'video',
            self::QUIZ => 'help-circle',
            self::ASSIGNMENT => 'clipboard',
            self::DISCUSSION => 'message-circle',
            self::WEBINAR => 'users',
        };
    }
}
