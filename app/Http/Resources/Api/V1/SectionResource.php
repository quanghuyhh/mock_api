<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
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
            "sectionId" => $this->id,
            "title" => $this->title,
            "subtitle" => $this->subtitle,
            "sectionItemType" => get_list_section_type($this->section_item_type),
            "layoutType" => get_list_section_layout($this->layout_type),
            "sectionItems" => SectionItemResource::collection($this->whenLoaded('items'))
        ];
    }
}
