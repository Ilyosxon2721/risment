<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TariffPlan extends Model
{
    protected $fillable = ['name', 'description', 'is_default', 'is_active'];
    
    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    public function items()
    {
        return $this->hasMany(TariffItem::class, 'plan_id');
    }
}
