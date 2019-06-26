<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class MetaDataResource extends JsonResource
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
            "metadataItemId" => $this->id,
            "value" => $this->value,
            "metaDataType" => MetaDataTypeResource::make($this->whenLoaded('type')),
        ];
    }
}
