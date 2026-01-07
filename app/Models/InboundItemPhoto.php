<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InboundItemPhoto extends Model
{
    protected $fillable = [
        'inbound_item_id',
        'photo_path',
        'description',
        'issue_type',
    ];

    public function inboundItem()
    {
        return $this->belongsTo(InboundItem::class);
    }
}
