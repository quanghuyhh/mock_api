<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoResource extends JsonResource
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
            'videoId' => (string) $this->id,
            'videoFileUrl' => $this->url ?? '',
            'duration' => (string) $this->duration,
            'thumbUrl' => $this->thumb_url ?? ''
        ];
    }
}
