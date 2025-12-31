<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OverageRule extends Model
{
    protected $fillable = [
        'code',
        'scope',
        'type',
        'pricing_mode',
        'fee_mgt',
        'fee_sgt',
        'fee_kgt',
        'fee',
    ];

    protected $casts = [
        'fee_mgt' => 'decimal:2',
        'fee_sgt' => 'decimal:2',
        'fee_kgt' => 'decimal:2',
        'fee' => 'decimal:2',
    ];

    // Code is immutable after creation
    protected static function booted()
    {
        static::updating(function ($rule) {
            if ($rule->isDirty('code')) {
                throw new \Exception('Overage rule code cannot be changed after creation.');
            }
        });
    }

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(SubscriptionPlan::class, 'plan_overage_rules');
    }
}
