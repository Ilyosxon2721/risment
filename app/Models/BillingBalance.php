<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class BillingBalance extends Model
{
    protected $fillable = [
        'company_id',
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BillingBalanceTransaction::class, 'company_id', 'company_id');
    }

    public static function getOrCreate(int $companyId): self
    {
        return self::firstOrCreate(
            ['company_id' => $companyId],
            ['balance' => 0]
        );
    }

    public function charge(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null): BillingBalanceTransaction
    {
        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId) {
            $this->decrement('balance', $amount);
            $this->refresh();

            return BillingBalanceTransaction::create([
                'company_id' => $this->company_id,
                'type' => 'charge',
                'amount' => -$amount,
                'balance_after' => $this->balance,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }

    public function topup(float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null): BillingBalanceTransaction
    {
        return DB::transaction(function () use ($amount, $description, $referenceType, $referenceId) {
            $this->increment('balance', $amount);
            $this->refresh();

            return BillingBalanceTransaction::create([
                'company_id' => $this->company_id,
                'type' => 'topup',
                'amount' => $amount,
                'balance_after' => $this->balance,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
            ]);
        });
    }
}
