<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class BookUnWrapperResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $result = [
            "bookId" => (string) $this->id,
            "title" => $this->title,
            "shortDescription" => $this->short_description,
            "overviewMarkDown" => $this->overview_markdown,
            "coverInfo" => isJSON($this->cover_info) ? json_decode($this->cover_info) : $this->cover_info,
            "authors" => AuthorCollection::collection($this->whenLoaded('authors')),
            "metadataItems" => MetaDataResource::collection($this->whenLoaded('metas')),
        ];

        return $result;
    }
}
