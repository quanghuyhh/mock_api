<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecentBook extends Model
{
    protected $fillable = ['book_id', 'user_id'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id');
    }
}
