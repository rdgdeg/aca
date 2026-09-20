<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasCover;
use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Deal extends Model implements HasMedia
{
    use HasCover, HasTranslations, InteractsWithMedia, TranslatableFallback;

    public array $translatable = ['title', 'description', 'conditions'];

    protected $fillable = [
        'merchant_id', 'title', 'description', 'conditions',
        'starts_on', 'ends_on', 'status', 'cover_url',
    ];

    protected function casts(): array
    {
        return [
            'starts_on' => 'date',
            'ends_on' => 'date',
            'status' => PublishStatus::class,
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        $today = now('Europe/Brussels')->toDateString();

        return $query->where('status', PublishStatus::Published)
            ->whereDate('starts_on', '<=', $today)
            ->whereDate('ends_on', '>=', $today);
    }

    public function daysLeft(): int
    {
        return max(0, now('Europe/Brussels')->startOfDay()->diffInDays($this->ends_on, false));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
    }
}
