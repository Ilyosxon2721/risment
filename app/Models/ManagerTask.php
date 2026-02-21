<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ManagerTask extends Model
{
    const TYPE_INBOUND = 'inbound';
    const TYPE_PICKPACK = 'pickpack';
    const TYPE_DELIVERY = 'delivery';
    const TYPE_STORAGE = 'storage';
    const TYPE_RETURN = 'return';

    const SOURCE_MANUAL = 'manual';
    const SOURCE_SELLERMIND = 'sellermind';

    const STATUS_PENDING = 'pending_confirmation';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'company_id',
        'created_by',
        'confirmed_by',
        'task_type',
        'source',
        'status',
        'source_type',
        'source_id',
        'details',
        'comment',
        'task_date',
        'confirmed_at',
    ];

    protected $casts = [
        'details' => 'array',
        'task_date' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function billingItems(): HasMany
    {
        return $this->hasMany(BillingItem::class, 'manager_task_id');
    }

    public function sourceable(): MorphTo
    {
        return $this->morphTo('sourceable', 'source_type', 'source_id');
    }

    // Scopes

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopeFromSellermind($query)
    {
        return $query->where('source', self::SOURCE_SELLERMIND);
    }

    public function scopeManual($query)
    {
        return $query->where('source', self::SOURCE_MANUAL);
    }

    // Accessors

    public function getTaskTypeLabelAttribute(): string
    {
        return match ($this->task_type) {
            self::TYPE_INBOUND => 'Приёмка',
            self::TYPE_PICKPACK => 'Сборка',
            self::TYPE_DELIVERY => 'Отгрузка',
            self::TYPE_STORAGE => 'Хранение',
            self::TYPE_RETURN => 'Возврат',
            default => $this->task_type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => 'Подтверждён',
            self::STATUS_REJECTED => 'Отклонён',
            self::STATUS_PENDING => 'Ожидает',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_CONFIRMED => 'green',
            self::STATUS_REJECTED => 'red',
            self::STATUS_PENDING => 'yellow',
            default => 'gray',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_SELLERMIND => 'SellerMind',
            default => 'Вручную',
        };
    }

    public function getTotalBilledAttribute(): int
    {
        return $this->billingItems()->sum('amount');
    }
}
