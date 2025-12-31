<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShipmentFbo extends Model
{
    protected $table = 'shipments_fbo';
    
    protected $fillable = [
        'company_id', 'marketplace', 'warehouse_name', 'planned_at', 'status', 'notes'
    ];
    
    protected $casts = [
        'planned_at' => 'datetime',
    ];
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function items()
    {
        return $this->hasMany(ShipmentItem::class, 'shipment_id');
    }
}
