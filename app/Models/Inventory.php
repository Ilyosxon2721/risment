<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';
    
    protected $fillable = [
        'company_id', 'sku_id', 'qty_total', 'qty_reserved', 'location_code'
    ];
    
    protected $casts = [
        'qty_total' => 'integer',
        'qty_reserved' => 'integer',
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function sku()
    {
        return $this->belongsTo(Sku::class);
    }
    
    public function getAvailableQtyAttribute()
    {
        return $this->qty_total - $this->qty_reserved;
    }
}
