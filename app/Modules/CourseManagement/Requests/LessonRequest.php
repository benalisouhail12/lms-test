<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manage-courses');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'position' => 'nullable|integer|min:0',
            'estimated_duration' => 'nullable|integer|min:1',
            'is_published' => 'nullable|boolean',
            'lesson_type' => 'required|string|in:TEXT,VIDEO,QUIZ,ASSIGNMENT,DISCUSSION,WEBINAR',
            'media' => 'nullable|file|max:100000',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10000',
        ];
    }
}
