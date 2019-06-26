<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "objectId" => $this->id,
            "title" => $this->title,
            "thumbImageUrl" => $this->thumb_image_url,
            "authorName" => $this->author_name,
            "shortDescription" => $this->short_description,
            "readingProgress" => (int) $this->reading_progress
        ];
    }
}
