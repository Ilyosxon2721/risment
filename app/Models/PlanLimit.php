<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanLimit extends Model
{
    protected $fillable = [
        'plan_id',
        'included_shipments',
        'included_boxes',
        'included_bags',
        'included_inbound_boxes',
    ];

    protected $casts = [
        'included_shipments' => 'integer',
        'included_boxes' => 'integer',
        'included_bags' => 'integer',
        'included_inbound_boxes' => 'integer',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
}
