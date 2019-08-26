<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RecentBookCollection extends ResourceCollection
{
    public static $wrap = 'recentBooks';

    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return RecentBookResource::collection($this->collection);
    }
}
