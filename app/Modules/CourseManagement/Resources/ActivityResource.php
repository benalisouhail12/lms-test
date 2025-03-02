<?php

namespace App\Modules\CourseManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class ActivityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'activity_type' => $this->activity_type,
            'lesson_id' => $this->lesson_id,
            'is_required' => $this->is_required,
            'points' => $this->points,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
