<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $recent = $this->recent ?? null;
        $lastView = null;
        if ($recent) {
            $lastView = $this->recent->updated_at ? $this->recent->updated_at->format(FORMAT_OUTPUT) : $lastView;
        }

        $progress = $this->progress ?? null;
        $readProgress = 0;
        if ($progress) {
            $readProgress = $progress->progress;
        }

        $count = 

        $data = [
            "bookInfo" => [
                "bookId" => (string) $this->id,
                "title" => $this->title,
                "shortDescription" => $this->short_description,
                "overviewMarkDown" => $this->overview_markdown,
                "coverInfo" => isJSON($this->cover_info) ? json_decode($this->cover_info) : $this->cover_info,
                "authors" => AuthorCollection::collection($this->whenLoaded('authors')),
                "metadataItems" => MetaDataResource::collection($this->whenLoaded('metas')),
                "categories" => CategoryResource::collection($this->whenLoaded('categories')),
                "readingProgress" => $readProgress,
                "lastViewAt" => $lastView ? convert_to_output_date($lastView) : null
            ],
            "summaryInfo" => SummaryResource::make($this->whenLoaded('summary')),
            "highlights" => NoteResource::collection($this->whenLoaded('highlights')),
        ];

        if ($this->relationLoaded('highlightsCount')) {
            $data['highlightCount'] = $this->highlightsCount->count();
        }

        $result = $data;
        if (empty($this->field))
            return $result;

        if ($this->field == BOOK_FIELD_INFO)
            $result = $data['bookInfo'];
        elseif ($this->field == BOOK_FIELD_SUMMARY)
            $result = $data['summaryInfo'];
        elseif ($this->field == BOOK_FIELD_METADATAS)
            $result = $data['bookInfo']['metadataItems'];
        elseif ($this->field == BOOK_FIELD_AUTHORS)
            $result = $data['bookInfo']['authors'];

        return $result;
    }
}
