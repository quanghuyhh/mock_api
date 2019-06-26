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
            "summaryId" => $this->id,
            "textInfo" => isJSON($this->text_info) ? json_decode($this->text_info) : $this->text_info,
            "audioInfo" => isJSON($this->audio_info) ? json_decode($this->audio_info) : $this->audio_info,
        ];
    }
}
