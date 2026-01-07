<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{
    protected $fillable = [
        'company_id', 'reference', 'planned_at', 'status', 'notes',
        'shipping_address', 'executor_name', 'executor_phone',
        'received_at', 'received_by', 'notes_receiving', 'has_discrepancies',
        'confirmed_at', 'confirmed_by_client'
    ];
    
    protected $casts = [
        'planned_at' => 'datetime',
        'received_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'has_discrepancies' => 'boolean',
    ];
    
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    
    public function items()
    {
        return $this->hasMany(InboundItem::class);
    }
    
    public function receivedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'received_by');
    }

    public function confirmedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirmed_by_client');
    }
}
