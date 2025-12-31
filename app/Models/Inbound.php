<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inbound extends Model
{
    protected $fillable = [
        'company_id', 'reference', 'planned_at', 'status', 'notes'
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
        return $this->hasMany(InboundItem::class);
    }
}
