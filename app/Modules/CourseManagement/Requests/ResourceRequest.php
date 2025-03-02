<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResourceRequest extends FormRequest
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
            'resource_type' => 'required|string|in:PDF,VIDEO,LINK,IMAGE,AUDIO,OTHER',
            'external_url' => 'nullable|url',
            'is_required' => 'nullable|boolean',
            'resource_file' => 'nullable|file|max:100000',
        ];
    }
}
