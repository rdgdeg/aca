<?php

namespace App\Models;

use App\Models\Concerns\HasCover;
use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Post extends Model implements HasMedia
{
    use HasCover, HasTranslations, InteractsWithMedia, TranslatableFallback;

    public array $translatable = ['title', 'slug', 'excerpt', 'body'];

    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'category',
        'published_at', 'is_press_release', 'cover_url', 'pdf_path',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_press_release' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now())->orderByDesc('published_at');
    }

    public function scopeNews(Builder $query): Builder
    {
        return $query->where('is_press_release', false);
    }

    public function scopePress(Builder $query): Builder
    {
        return $query->where('is_press_release', true);
    }

    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where(function (Builder $q) use ($slug) {
            $q->where('slug->fr', $slug)->orWhere('slug->nl', $slug)->orWhere('slug->en', $slug);
        });
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('files');
    }
}
