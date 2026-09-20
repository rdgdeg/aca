<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['title', 'slug', 'blocks', 'seo_title', 'seo_description'];

    protected $fillable = [
        'title', 'slug', 'blocks', 'seo_title', 'seo_description', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where(function (Builder $q) use ($slug) {
            $q->where('slug->fr', $slug)->orWhere('slug->nl', $slug)->orWhere('slug->en', $slug);
        });
    }

    public function blocksForLocale(?string $locale = null): array
    {
        $locale ??= aca_locale();
        $blocks = $this->getTranslation('blocks', $locale, false);

        if (is_string($blocks) && $blocks !== '') {
            $decoded = json_decode($blocks, true);
            $blocks = is_array($decoded) ? $decoded : [['type' => 'text', 'body' => $blocks]];
        }

        if (! is_array($blocks) || $blocks === []) {
            $blocks = $this->getTranslation('blocks', 'fr', false);
        }

        return is_array($blocks) ? $blocks : [];
    }
}
