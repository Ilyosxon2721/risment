<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SellermindAccountLink extends Model
{
    protected $fillable = [
        'company_id',
        'sellermind_user_id',
        'sellermind_company_id',
        'link_token',
        'sync_products',
        'sync_orders',
        'sync_stock',
        'status',
        'linked_at',
    ];

    protected $casts = [
        'sync_products' => 'boolean',
        'sync_orders' => 'boolean',
        'sync_stock' => 'boolean',
        'linked_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public static function generateToken(): string
    {
        return Str::random(64);
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => __('Pending'),
            'active' => __('Active'),
            'disabled' => __('Disabled'),
            default => $this->status,
        };
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'active' => 'badge-success',
            'pending' => 'badge-warning',
            'disabled' => 'badge-error',
            default => 'badge-secondary',
        };
    }
}
