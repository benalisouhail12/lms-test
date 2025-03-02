<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
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
            'position' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ];
    }
}
