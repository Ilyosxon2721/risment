<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanyDiscount extends Model
{
    protected $fillable = [
        'company_id', 'type', 'target', 'value', 'reason', 'starts_at', 'ends_at', 'created_by',
    ];

    protected $casts = [
        'value'      => 'decimal:2',
        'starts_at'  => 'datetime',
        'ends_at'    => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Скидка активна прямо сейчас */
    public function isActive(): bool
    {
        $now = now();
        if ($this->starts_at && $this->starts_at->gt($now)) return false;
        if ($this->ends_at   && $this->ends_at->lt($now))   return false;
        return true;
    }

    /** Применить скидку к сумме */
    public function apply(float $amount): float
    {
        if ($this->type === 'percent') {
            return $amount * (1 - $this->value / 100);
        }
        return max(0, $amount - $this->value);
    }

    /** Scope: только активные сейчас */
    public function scopeActive($query)
    {
        return $query
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')  ->orWhere('ends_at',   '>=', now()));
    }

    /** Scope: по target (subscription, overage, all) */
    public function scopeForTarget($query, string $target)
    {
        return $query->where(fn ($q) => $q->where('target', $target)->orWhere('target', 'all'));
    }

    public function getTargetLabelAttribute(): string
    {
        return match ($this->target) {
            'subscription' => 'Подписка',
            'overage'      => 'Сверхлимит',
            'all'          => 'Всё',
            default        => $this->target,
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'percent' ? "%  ({$this->value}%)" : "Фикс. ({$this->value} UZS)";
    }
}
