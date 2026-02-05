<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BillingInvoiceItem extends Model
{
    protected $table = 'billing_invoice_items';

    protected $fillable = [
        'invoice_id',
        'billing_item_id',
        'scope',
        'title_ru',
        'title_uz',
        'unit_price',
        'qty',
        'amount',
    ];

    protected $casts = [
        'unit_price' => 'integer',
        'qty' => 'decimal:2',
        'amount' => 'integer',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(BillingInvoice::class, 'invoice_id');
    }

    public function billingItem(): BelongsTo
    {
        return $this->belongsTo(BillingItem::class, 'billing_item_id');
    }

    public function getTitle(): string
    {
        $locale = app()->getLocale();
        return $locale === 'uz' ? $this->title_uz : $this->title_ru;
    }
}
