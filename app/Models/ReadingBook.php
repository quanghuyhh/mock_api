<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingBook extends Model
{
    protected $fillable = ['book_id', 'user_id', 'progress'];
}
