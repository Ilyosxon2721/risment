<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $fillable = [
        'company_id', 'sku_code', 'barcode', 'title', 
        'dims_l', 'dims_w', 'dims_h', 'weight', 'photo_path', 'is_active'
    ];
    
    protected $casts = [
        'dims_l' => 'decimal:2',
        'dims_w' => 'decimal:2',
        'dims_h' => 'decimal:2',
        'weight' => 'decimal:3',
        'is_active' => 'boolean',
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function inventory()
    {
        return $this->hasOne(Inventory::class);
    }
    
    public function inboundItems()
    {
        return $this->hasMany(InboundItem::class);
    }
    
    public function shipmentItems()
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
