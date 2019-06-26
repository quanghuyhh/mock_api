<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    protected $table = 'meta_datas';

    protected $fillable = ['value', 'type_id'];

    public function type() {
        return $this->belongsTo(MetaDataType::class, 'type_id', 'id');
    }
}
