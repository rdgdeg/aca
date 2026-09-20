<?php

namespace App\Models;

use App\Enums\PublishStatus;
use App\Models\Concerns\HasCover;
use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Event extends Model implements HasMedia
{
    use HasCover, HasTranslations, InteractsWithMedia, TranslatableFallback;

    public array $translatable = ['title', 'slug', 'description'];

    protected $fillable = [
        'title', 'slug', 'description', 'starts_at', 'ends_at', 'recurrence_rule',
        'merchant_id', 'event_type_id', 'location', 'lat', 'lng', 'price',
        'external_url', 'form_id', 'capacity', 'registration_deadline', 'waitlist',
        'status', 'is_featured', 'cover_url', 'proposed_by_submission_id',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'registration_deadline' => 'datetime',
            'waitlist' => 'boolean',
            'is_featured' => 'boolean',
            'status' => PublishStatus::class,
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PublishStatus::Published);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()
            ->where(function (Builder $q) {
                $q->where('starts_at', '>=', now()->startOfDay())
                    ->orWhere(function (Builder $q2) {
                        $q2->whereNotNull('ends_at')->where('ends_at', '>=', now()->startOfDay());
                    });
            })
            ->orderBy('starts_at');
    }

    public static function spotlight(): ?self
    {
        return static::upcoming()->where('is_featured', true)->first()
            ?? static::upcoming()->first();
    }

    public function scopePast(Builder $query): Builder
    {
        return $query->published()
            ->where(function (Builder $q) {
                $q->where(function (Builder $q2) {
                    $q2->whereNull('ends_at')->where('starts_at', '<', now()->startOfDay());
                })->orWhere(function (Builder $q2) {
                    $q2->whereNotNull('ends_at')->where('ends_at', '<', now()->startOfDay());
                });
            })
            ->orderByDesc('starts_at');
    }

    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where(function (Builder $q) use ($slug) {
            $q->where('slug->fr', $slug)->orWhere('slug->nl', $slug)->orWhere('slug->en', $slug);
        });
    }

    public function remainingPlaces(): ?int
    {
        if (! $this->capacity) {
            return null;
        }

        return max(0, $this->capacity - $this->submissions()->count());
    }

    public function registrationOpen(): bool
    {
        if (! $this->form_id) {
            return false;
        }
        if ($this->registration_deadline && now()->greaterThan($this->registration_deadline)) {
            return false;
        }
        if ($this->capacity && $this->remainingPlaces() <= 0 && ! $this->waitlist) {
            return false;
        }

        return true;
    }

    public function icsUrl(): string
    {
        return aca_path('event', ['slug' => $this->t('slug')]).'.ics';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
    }
}
