<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
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
            'activity_type' => 'required|string|in:QUIZ,ASSIGNMENT,DISCUSSION',
            'is_required' => 'nullable|boolean',
            'points' => 'nullable|integer|min:0',
            'due_date' => 'nullable|date|after_or_equal:today',
        ];
    }
}
