<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Highlight extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'user_id',
        'book_id',
        'highlight_ref',
        'note',
        'quote',
        'short_desc',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
