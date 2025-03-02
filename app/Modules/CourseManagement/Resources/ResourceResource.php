<?php

namespace App\Modules\CourseManagement\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
class ResourceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'resource_type' => $this->resource_type,
            'external_url' => $this->external_url,
            'lesson_id' => $this->lesson_id,
            'is_required' => $this->is_required,
            'file_url' => $this->getFirstMediaUrl('resource_file'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
