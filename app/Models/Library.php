<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $fillable = [
        'book_id',
        'user_id',
        'status',
        'progress',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
