<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable =
    [
        'category_name'
    ];

    // Relationship dengan Shoes
    public function shoes()
    {
        return $this->hasMany(Shoes::class, 'category_id');
    }

    // Accessor untuk menghitung jumlah sepatu
    public function getShoesCountAttribute()
    {
        return $this->shoes()->count();
    }
}
