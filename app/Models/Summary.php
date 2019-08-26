<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Text;

class Summary extends Model
{
    protected $fillable = ['text_id', 'video_id', 'audio_id'];

    public function text()
    {
        return $this->belongsTo(Text::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function audio()
    {
        return $this->belongsTo(Audio::class);
    }
}
