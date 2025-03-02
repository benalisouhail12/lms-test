<?php

namespace App\Modules\CourseManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'short_description' => $this->short_description,
            'learning_objectives' => $this->learning_objectives,
            'course_type' => [
                'value' => $this->course_type->value,
                'label' => $this->course_type->label(),
            ],
            'status' => [
                'value' => $this->status->value,
                'label' => $this->status->label(),
                'color' => $this->status->color(),
            ],
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'level' => $this->level,
            'duration_in_weeks' => $this->duration_in_weeks,
            'credit_hours' => $this->credit_hours,
            'capacity' => $this->capacity,
            'department' => $this->whenLoaded('department', function () {
                return [
                    'id' => $this->department->id,
                    'name' => $this->department->name,
                ];
            }),
            'program' => $this->whenLoaded('program', function () {
                return [
                    'id' => $this->program->id,
                    'name' => $this->program->name,
                ];
            }),
            'instructors' => $this->whenLoaded('instructors', function () {
                return $this->instructors->map(function ($instructor) {
                    return [
                        'id' => $instructor->id,
                        'name' => $instructor->name,
                        'email' => $instructor->email,
                    ];
                });
            }),
            'sections' => $this->whenLoaded('sections', function () {
                return SectionResource::collection($this->sections);
            }),
            'learning_paths' => $this->whenLoaded('learningPaths', function () {
                return $this->learningPaths->map(function ($path) {
                    return [
                        'id' => $path->id,
                        'title' => $path->title,
                        'position' => $path->pivot->position,
                    ];
                });
            }),
            'prerequisites' => $this->whenLoaded('prerequisites', function () {
                return $this->prerequisites->map(function ($prereq) {
                    return [
                        'id' => $prereq->id,
                        'title' => $prereq->title,
                    ];
                });
            }),
            'media' => [
                'thumbnail' => $this->getFirstMediaUrl('course_thumbnail'),
                'banner' => $this->getFirstMediaUrl('course_banner'),
            ],
            'student_count' => $this->whenCounted('students'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}









