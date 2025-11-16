<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoeSize extends Model
{
    protected $fillable = [
        'shoe_id',
        'size',
        'stock',
    ];
}
