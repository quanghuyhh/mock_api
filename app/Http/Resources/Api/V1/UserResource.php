<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $createdAt = Carbon::createFromFormat(FORMAT_INPUT, $this->created_at)->format(FORMAT_OUTPUT);
        $updatedAt = Carbon::createFromFormat(FORMAT_INPUT, $this->updated_at)->format(FORMAT_OUTPUT);
        return [
            "id"=> $this->id,
            "name"=> $this->name,
            "email"=> $this->email,
            "email_verified_at"=> $this->email_verified_at,
            "created_at"=> str_replace('_', 'T', $createdAt),
            "updated_at"=> str_replace('_', 'T', $updatedAt),
            "key" => $this->id,
        ];
    }
}
