<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffAuditLog extends Model
{
    const UPDATED_AT = null; // Only created_at

    protected $fillable = [
        'user_id',
        'entity_type',
        'entity_id',
        'before_json',
        'after_json',
    ];

    protected $casts = [
        'before_json' => 'array',
        'after_json' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
