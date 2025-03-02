<?php

namespace App\Modules\CourseManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class LessonResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'content' => $this->content,
            'position' => $this->position,
            'section_id' => $this->section_id,
            'estimated_duration' => $this->estimated_duration,
            'is_published' => $this->is_published,
            'lesson_type' => $this->lesson_type,
            'media_url' => $this->getFirstMediaUrl('lesson_media'),
            'attachments' => $this->getMedia('attachments')->map(function ($media) {
                return [
                    'id' => $media->id,
                    'name' => $media->name,
                    'file_name' => $media->file_name,
                    'mime_type' => $media->mime_type,
                    'size' => $media->size,
                    'url' => $media->getUrl(),
                ];
            }),
            'resources' => $this->whenLoaded('resources', function () {
                return ResourceResource::collection($this->resources);
            }),
            'activities' => $this->whenLoaded('activities', function () {
                return ActivityResource::collection($this->activities);
            }),
            'progress' => $this->whenLoaded('progress', function () {
                return $this->progress->first() ? [
                    'status' => $this->progress->first()->status,
                    'viewed_at' => $this->progress->first()->viewed_at,
                    'completed_at' => $this->progress->first()->completed_at,
                    'time_spent' => $this->progress->first()->time_spent,
                ] : null;
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
