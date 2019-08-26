<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AudioResource extends JsonResource
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
            'audioId' => (string) $this->id,
            'audioFileUrl' => $this->url ?? '',
            'duration' => (string) $this->duration,
        ];
    }
}
