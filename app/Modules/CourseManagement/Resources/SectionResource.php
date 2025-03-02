<?php

namespace App\Modules\CourseManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class SectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'position' => $this->position,
            'is_published' => $this->is_published,
            'course_id' => $this->course_id,
            'lessons' => $this->whenLoaded('lessons', function () {
                return LessonResource::collection($this->lessons);
            }),
            'lessons_count' => $this->whenCounted('lessons'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
