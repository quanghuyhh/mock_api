<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    public $fillable = ['title', 'short_description', 'overview_markdown', 'cover_info'];

    protected $casts = [
        'cover_info' => 'array'
    ];

    public function authors () {
        return $this->belongsToMany(Author::class, 'book_author', 'book_id', 'author_id');
    }

    public function metas () {
        return $this->belongsToMany(MetaData::class, 'book_meta_data', 'book_id', 'meta_data_id');
    }

    public function summary () {
        return $this->belongsToMany(Summary::class, 'book_summary', 'book_id', 'summary_id');
    }

    public function summaries () {
        return $this->belongsToMany(Summary::class, 'book_summary', 'book_id', 'summary_id');
    }

    public function categories () {
        return $this->belongsToMany(Category::class, 'book_categories', 'book_id', 'category_id');
    }

    public function highlights () {
        return $this->hasMany(Highlight::class, 'book_id');
    }

    public function highlightsCount () {
        return $this->hasMany(Highlight::class, 'book_id');
    }

    public function recent() {
        return $this->hasOne(RecentBook::class, 'book_id');
    }

    public function progress() {
        return $this->hasOne(ReadingBook::class, 'book_id');
    }
}
