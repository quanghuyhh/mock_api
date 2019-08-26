<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class SummaryResource extends JsonResource
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
            "summaryId" => (string) $this->id,
            "textInfo" => TextResource::make($this->whenLoaded('text')),
            "audioInfo" => AudioResource::make($this->whenLoaded('audio')),
            "videoInfo" => VideoResource::make($this->whenLoaded('video')),
        ];
    }
}
