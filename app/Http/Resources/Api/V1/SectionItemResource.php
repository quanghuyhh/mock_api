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
        $covers = $this->cover_info && isJSON($this->cover_info) ? (object) json_decode($this->cover_info) : null;
        $thumb = $covers && !empty($covers) ? $covers->original : '';
        $author = !empty($this->authors) ? $this->authors[0]->name : '';
        $progress = $this->progress ? $this->progress->progress : 0;
        
        return [
            "objectId" => (string) $this->id,
            "title" => $this->title,
            "thumbImageUrl" => $thumb,
            "authorName" => $author,
            "shortDescription" => $this->short_description ?? '',
            "readingProgress" => (int) $progress
        ];
    }
}
