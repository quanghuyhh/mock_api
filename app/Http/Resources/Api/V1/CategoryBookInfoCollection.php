<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryBookInfoCollection extends ResourceCollection
{
    public static $wrap = 'bookInfos';
    
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return BookInfoResource::collection($this->collection);
    }
}
