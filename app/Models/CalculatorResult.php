<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalculatorResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'calculation_data',
        'result_data',
        'recommended_plan',
        'total_cost',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'calculation_data' => 'array',
            'result_data' => 'array',
            'total_cost' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
