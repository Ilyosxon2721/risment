<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ProductVariantImage extends Model
{
    protected $fillable = [
        'product_variant_id',
        'image_path',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the variant that owns the image
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Get the full URL of the image
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    /**
     * Delete the image file when model is deleted
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($image) {
            if (Storage::exists($image->image_path)) {
                Storage::delete($image->image_path);
            }
        });
    }
}
