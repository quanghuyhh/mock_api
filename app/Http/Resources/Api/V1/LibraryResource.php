<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Book;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LibraryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $updatedAt = $this->updated_at ? Carbon::createFromFormat(FORMAT_INPUT, $this->updated_at)->format(FORMAT_OUTPUT) : Carbon::now()->format(FORMAT_OUTPUT);
        BookResource::withoutWrapping();
        return [
            "libraryItemId" => (string) $this->book_id,
            // "status" => get_library_status($this->status),
            "lastUpdate" => convert_to_output_date($updatedAt) ?? '',
            "bookInfo" => BookInfoResource::make($this->whenLoaded('book'))
        ];
    }
}
