<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id', 'sku_id', 'product_variant_id', 'item_name', 'qty', 'price',
    ];
    
    protected $casts = [
        'qty' => 'integer',
    ];
    
    public function shipment()
    {
        return $this->belongsTo(ShipmentFbo::class, 'shipment_id');
    }
    
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
}
