<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShoesVariant extends Model
{
    protected $fillable = [
        'shoe_id',
        'color',
        'color_code',
        'size',
        'price',
        'stock',
        'sku',
        'is_available'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'size' => 'integer',
        'is_available' => 'boolean',
    ];

    // Auto-generate SKU
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($variant) {
            if (empty($variant->sku)) {
                $variant->sku = strtoupper(
                    substr($variant->shoe->name, 0, 3) .
                        '-' . $variant->color .
                        '-' . $variant->size .
                        '-' . time()
                );
            }
        });
    }

    // Relationship: Variant belongs to 1 shoe
    public function shoes()
    {
        return $this->belongsTo(Shoes::class);
    }

    // Relationship: 1 variant has many images
    public function images()
    {
        return $this->hasMany(VariantImages::class, 'shoe_variant_id');
    }

    // Get primary image
    public function getPrimaryImageAttribute()
    {
        return $this->images()
            ->where('is_primary', true)
            ->first() ?? $this->images()->first();
    }

    // Check if in stock
    public function isInStock()
    {
        return $this->stock > 0 && $this->is_available;
    }

    // Accessor: Formatted price
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
