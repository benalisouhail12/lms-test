<?php

namespace App\Modules\CourseManagement\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnrollmentRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user()->can('manage-enrollments');
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'status' => 'nullable|string|in:PENDING,ACTIVE,COMPLETED,DROPPED',
        ];
    }
}
