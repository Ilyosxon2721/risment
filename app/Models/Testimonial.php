<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'author_name',
        'category_ru',
        'category_uz',
        'text_ru',
        'text_uz',
        'rating',
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

    public function getText(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->text_uz ?? $this->text_ru)
            : $this->text_ru;
    }

    public function getCategory(): string
    {
        return app()->getLocale() === 'uz' 
            ? ($this->category_uz ?? $this->category_ru)
            : $this->category_ru;
    }
}
