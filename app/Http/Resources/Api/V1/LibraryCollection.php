<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LibraryCollection extends ResourceCollection
{
    public static $wrap = 'libraryItems';
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return LibraryResource::collection($this->collection);
    }
}
