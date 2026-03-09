<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'inn', 'contact_name', 'phone', 'email', 'address', 'status', 'manager_user_id',
        'subscription_plan_id', 'balance', 'telegram_chat_id', 'telegram_settings',
    ];

    protected function casts(): array
    {
        return [
            'telegram_settings' => 'array',
        ];
    }
    
    public function users()
    {
        return $this->belongsToMany(User::class, 'company_user')
            ->withPivot('role_in_company')
            ->withTimestamps();
    }
    
    public function skus()
    {
        return $this->hasMany(Sku::class);
    }
    
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    public function sellermindLink()
    {
        return $this->hasOne(SellermindAccountLink::class);
    }

    public function billingSubscription()
    {
        return $this->hasOne(BillingSubscription::class)->where('status', 'active');
    }

    public function billingBalance()
    {
        return $this->hasOne(BillingBalance::class);
    }

    public function billingInvoices()
    {
        return $this->hasMany(BillingInvoice::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function managerTasks()
    {
        return $this->hasMany(ManagerTask::class);
    }

    public function discounts()
    {
        return $this->hasMany(CompanyDiscount::class);
    }

    public function billingTransactions()
    {
        return $this->hasMany(BillingBalanceTransaction::class);
    }

    /**
     * Apply all active discounts for a given target to an amount.
     * target: 'subscription' | 'overage' | 'all'
     */
    public function applyDiscounts(float $amount, string $target): float
    {
        $discounts = $this->discounts()->active()->forTarget($target)->get();
        foreach ($discounts as $discount) {
            $amount = $discount->apply($amount);
        }
        return $amount;
    }

    public function getFormattedBalanceAttribute(): string
    {
        return number_format(abs($this->balance), 0, '', ' ') . ' ' . __('UZS');
    }
}
