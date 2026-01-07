<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundItem extends Model
{
    protected $fillable = [
        'inbound_id', 'variant_id', 'qty_planned', 'qty_received', 'qty_diff', 'notes'
    ];
    
    protected $casts = [
        'qty_planned' => 'integer',
        'qty_received' => 'integer',
        'qty_diff' => 'integer',
    ];
    
    public function inbound()
    {
        return $this->belongsTo(Inbound::class);
    }
    
    public function variant()
    {
        return $this->belongsTo(\App\Models\ProductVariant::class, 'variant_id');
    }
    
    public function photos()
    {
        return $this->hasMany(InboundItemPhoto::class);
    }
}
