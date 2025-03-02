<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Modules\CourseManagement\Enums\CourseType;
use App\Modules\CourseManagement\Enums\CourseStatus;

class CourseRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manage-courses');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique('courses')->ignore($this->course),
            ],
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:255',
            'learning_objectives' => 'nullable|array',
            'course_type' => [
                'required',
                Rule::enum(CourseType::class),
            ],
            'status' => [
                'required',
                Rule::enum(CourseStatus::class),
            ],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'level' => 'nullable|string|in:BEGINNER,INTERMEDIATE,ADVANCED',
            'duration_in_weeks' => 'nullable|integer|min:1',
            'credit_hours' => 'nullable|integer|min:0',
            'capacity' => 'nullable|integer|min:1',
            'department_id' => 'nullable|exists:departments,id',
            'program_id' => 'nullable|exists:programs,id',
            'instructor_ids' => 'nullable|array',
            'instructor_ids.*' => 'exists:users,id',
            'thumbnail' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',
        ];
    }
}











