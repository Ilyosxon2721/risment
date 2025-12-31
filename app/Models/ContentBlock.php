<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentBlock extends Model
{
    protected $fillable = [
        'page_slug',
        'block_key',
        'title_ru',
        'title_uz',
        'body_ru',
        'body_uz',
        'sort',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get title in current locale
     */
    public function getTitle(): ?string
    {
        return app()->getLocale() === 'ru' ? $this->title_ru : $this->title_uz;
    }

    /**
     * Get body in current locale
     */
    public function getBody(): ?string
    {
        return app()->getLocale() === 'ru' ? $this->body_ru : $this->body_uz;
    }

    /**
     * Get block by page and key
     */
    public static function getBlock(string $pageSlug, string $blockKey): ?self
    {
        return self::where('page_slug', $pageSlug)
            ->where('block_key', $blockKey)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Get all blocks for a page
     */
    public static function getPageBlocks(string $pageSlug): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('page_slug', $pageSlug)
            ->where('is_active', true)
            ->orderBy('sort')
            ->get();
    }
}
