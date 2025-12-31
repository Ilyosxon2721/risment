<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'name', 'phone', 'company_name', 'marketplaces', 'schemes', 'comment', 'source_page', 'status',
    ];
    
    protected $casts = [
        'marketplaces' => 'array',
        'schemes' => 'array',
    ];
}
