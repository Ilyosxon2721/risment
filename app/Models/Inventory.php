<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'company_id', 'sku_id', 'product_variant_id', 'qty_total', 'qty_reserved', 'location_code'
    ];
    
    protected $casts = [
        'qty_total' => 'integer',
        'qty_reserved' => 'integer',
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    /**
     * Old SKU relationship (deprecated, for backward compatibility)
     */
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
    
    /**
     * New ProductVariant relationship
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    
    public function getAvailableQtyAttribute()
    {
        return $this->qty_total - $this->qty_reserved;
    }
}
