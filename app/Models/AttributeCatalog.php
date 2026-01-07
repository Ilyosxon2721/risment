<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeCatalog extends Model
{
    protected $table = 'attribute_catalog';
    
    protected $fillable = [
        'name',
        'type',
        'options',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active attributes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered attributes
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort')->orderBy('name');
    }
}
