<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentItem extends Model
{
    protected $fillable = [
        'shipment_id', 'sku_id', 'qty'
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
