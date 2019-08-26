<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResponseData extends Model
{
    protected $fillable = ['code', 'message', 'description'];
}
