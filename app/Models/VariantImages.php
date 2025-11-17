<?php

namespace App\Models;

use Illuminate\Container\Attributes\Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class VariantImages extends Model
{
    protected $fillable = [
        'shoe_variant_id',
        'image_path',
        'is_primary',
        'order'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'order' => 'integer',
    ];

    // Relationship: Image belongs to 1 variant
    public function shoesVariant()
    {
        return $this->belongsTo(ShoesVariant::class, 'shoe_variant_id');
    }

    // Accessor: Get full URL
    public function getImageUrlAttribute()
    {
        return FacadesStorage::url($this->image_path);
    }

    // Delete image file when model deleted
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            FacadesStorage::delete($image->image_path);
        });
    }
}
