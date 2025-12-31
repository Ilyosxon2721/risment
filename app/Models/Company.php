<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name', 'inn', 'contact_name', 'phone', 'email', 'address', 'status', 'manager_user_id',
    ];
    
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
    
    public function getFormattedBalanceAttribute(): string
    {
        return number_format(abs($this->balance), 0, '', ' ') . ' ' . __('UZS');
    }
}
