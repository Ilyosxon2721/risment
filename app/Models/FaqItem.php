<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = [
        'question_ru',
        'question_uz',
        'answer_ru',
        'answer_uz',
        'category',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getQuestion(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->question_uz ?? $this->question_ru)
            : $this->question_ru;
    }

    public function getAnswer(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->answer_uz ?? $this->answer_ru)
            : $this->answer_ru;
    }
}
