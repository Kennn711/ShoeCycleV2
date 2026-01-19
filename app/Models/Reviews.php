<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'user_id',
        'shoe_id',
        'transaction_id',
        'rating',
        'comment',
    ];
}
