<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Summary extends Model
{
    protected $fillable = ['text_info', 'audio_info'];

    protected $casts = [
        'text_info' => 'array',
        'audio_info' => 'array'
    ];
}
