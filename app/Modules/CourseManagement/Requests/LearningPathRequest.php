<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LearningPathRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manage-learning-paths');
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:DRAFT,PUBLISHED,ARCHIVED',
            'courses' => 'nullable|array',
            'courses.*.id' => 'required|exists:courses,id',
            'courses.*.position' => 'required|integer|min:0',
        ];
    }
}
