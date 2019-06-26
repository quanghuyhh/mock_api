<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    protected $fillable = ['title', 'thumb_image_url', 'author_name', 'short_description', 'reading_progress'];
}
