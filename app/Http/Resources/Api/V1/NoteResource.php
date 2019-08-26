<?php

namespace App\Http\Resources\Api\V1;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'highlighId' => (string) $this->id,
            'highlightRef' => $this->highlight_ref ?? '',
            'note' => $this->note ?? '',
            'quote' => $this->quote ?? '',
            'createAt' => convert_to_output_date($this->created_at ? $this->created_at->format(FORMAT_OUTPUT) : Carbon::now()->format(FORMAT_OUTPUT)),
            'updateAt' => convert_to_output_date($this->updated_at ? $this->updated_at->format(FORMAT_OUTPUT) : Carbon::now()->format(FORMAT_OUTPUT)),
        ];
    }
}
