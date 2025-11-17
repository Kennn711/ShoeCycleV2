<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Pest\Support\Str;

class Shoes extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'description',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($shoe) {
            if (empty($shoe->slug)) {
                $shoe->slug = Str::slugify($shoe->name);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relationship: 1 shoe has many variants
    public function variants()
    {
        return $this->hasMany(ShoesVariant::class);
    }

    // Accessor: Get all available colors
    public function getAvailableColorsAttribute()
    {
        return $this->variants()
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->select('color', 'color_code')
            ->distinct()
            ->get();
    }

    // Accessor: Get all available sizes
    public function getAvailableSizesAttribute()
    {
        return $this->variants()
            ->where('is_available', true)
            ->where('stock', '>', 0)
            ->pluck('size')
            ->unique()
            ->sort()
            ->values();
    }

    // Accessor: Get price range
    public function getPriceRangeAttribute()
    {
        $prices = $this->variants()->pluck('price');
        $min = $prices->min();
        $max = $prices->max();

        if ($min == $max) {
            return 'Rp ' . number_format($min, 0, ',', '.');
        }

        return 'Rp ' . number_format($min, 0, ',', '.') . ' - Rp ' . number_format($max, 0, ',', '.');
    }

    // Accessor: Total stock
    public function getTotalStockAttribute()
    {
        return $this->variants()->sum('stock');
    }
}
