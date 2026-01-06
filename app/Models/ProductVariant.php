<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'variant_name',
        'sku_code',
        'barcode',
        'dims_l',
        'dims_w',
        'dims_h',
        'weight',
        'price',
        'cost_price',
        'expenses',
        'is_active',
    ];

    protected $casts = [
        'dims_l' => 'decimal:2',
        'dims_w' => 'decimal:2',
        'dims_h' => 'decimal:2',
        'weight' => 'decimal:3',
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'expenses' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the variant
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attributes for the variant
     */
    public function attributes(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }

    /**
     * Get the images for the variant
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductVariantImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary image
     */
    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductVariantImage::class)->where('is_primary', true);
    }

    /**
     * Get the marketplace links for the variant
     */
    public function marketplaceLinks(): HasMany
    {
        return $this->hasMany(MarketplaceProductLink::class);
    }

    /**
     * Get the inventory for the variant
     */
    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    /**
     * Get the company via product
     */
    public function company()
    {
        return $this->hasOneThrough(Company::class, Product::class, 'id', 'id', 'product_id', 'company_id');
    }

    /**
     * Get dimensions as string
     */
    public function getDimensionsAttribute(): ?string
    {
        if ($this->dims_l && $this->dims_w && $this->dims_h) {
            return "{$this->dims_l}×{$this->dims_w}×{$this->dims_h} см";
        }
        return null;
    }

    /**
     * Scope active variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
