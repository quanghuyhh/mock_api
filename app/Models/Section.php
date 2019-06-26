<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['title', 'subtitle', 'section_item_type', 'layout_type'];

    public function items() {
        return $this->belongsToMany(SectionItem::class, 'section_section_item', 'section_id', 'item_id');
    }
}
