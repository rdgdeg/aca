<?php

namespace App\Models;

use App\Enums\MerchantStatus;
use App\Models\Concerns\HasCover;
use App\Services\OpeningStatus;
use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Translatable\HasTranslations;

class Merchant extends Model implements HasMedia
{
    use HasCover, HasTranslations, InteractsWithMedia, LogsActivity, TranslatableFallback;

    public array $translatable = ['slug', 'description', 'short_text', 'highlights', 'brands', 'seo_title', 'seo_description'];

    protected $fillable = [
        'name', 'slug', 'description', 'short_text', 'highlights', 'brands',
        'address', 'postal_code', 'city', 'lat', 'lng', 'phone', 'email', 'website',
        'vat_number', 'status', 'is_featured', 'member_since', 'holiday_closed',
        'open_sundays', 'internal_notes', 'search_text', 'cover_url',
        'seo_title', 'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'status' => MerchantStatus::class,
            'is_featured' => 'boolean',
            'holiday_closed' => 'boolean',
            'open_sundays' => 'boolean',
            'member_since' => 'date',
            'lat' => 'float',
            'lng' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Merchant $merchant) {
            $merchant->search_text = aca_normalize(implode(' ', [
                $merchant->name,
                $merchant->address,
                $merchant->t('description', 'fr'),
                $merchant->t('short_text', 'fr'),
                $merchant->t('brands', 'fr'),
                $merchant->t('highlights', 'fr'),
            ]));
        });
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class);
    }

    public function openingHours(): HasMany
    {
        return $this->hasMany(OpeningHour::class)->orderBy('weekday')->orderBy('position');
    }

    public function closures(): HasMany
    {
        return $this->hasMany(ClosurePeriod::class);
    }

    public function socialLinks(): HasMany
    {
        return $this->hasMany(SocialLink::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class);
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(ChangeSuggestion::class);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', MerchantStatus::Published);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $normalized = aca_normalize($term);
        if ($normalized === '') {
            return $query;
        }

        return $query->where('search_text', 'like', '%'.$normalized.'%');
    }

    public function scopeWithSlug(Builder $query, string $slug): Builder
    {
        return $query->where(function (Builder $q) use ($slug) {
            $q->where('slug->fr', $slug)
                ->orWhere('slug->nl', $slug)
                ->orWhere('slug->en', $slug);
        });
    }

    public function openingStatus(): array
    {
        return app(OpeningStatus::class)->for($this);
    }

    public function mapsUrl(): string
    {
        $query = urlencode(trim($this->address.' '.$this->postal_code.' '.$this->city));

        return 'https://www.google.com/maps/dir/?api=1&destination='.$query;
    }

    public function appleMapsUrl(): string
    {
        $query = urlencode(trim($this->address.' '.$this->postal_code.' '.$this->city));

        return 'https://maps.apple.com/?daddr='.$query;
    }

    public function directionsUrl(): string
    {
        $agent = (string) request()->userAgent();

        if (preg_match('/iPhone|iPad|Macintosh/', $agent) === 1) {
            return $this->appleMapsUrl();
        }

        return $this->mapsUrl();
    }

    public function distanceKm(float $lat, float $lng): ?float
    {
        if ($this->lat === null || $this->lng === null) {
            return null;
        }

        $earth = 6371;
        $dLat = deg2rad($lat - $this->lat);
        $dLng = deg2rad($lng - $this->lng);
        $a = sin($dLat / 2) ** 2 + cos(deg2rad($this->lat)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        return round($earth * 2 * atan2(sqrt($a), sqrt(1 - $a)), 2);
    }

    public function obfuscatedEmail(): ?string
    {
        return $this->email ? str_replace('@', ' (at) ', $this->email) : null;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('gallery')->onlyKeepLatest(12);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('webp')->format('webp')->nonQueued();
        $this->addMediaConversion('card')->width(900)->height(600)->format('webp')->nonQueued();
        $this->addMediaConversion('hero')->width(1600)->height(900)->format('webp')->nonQueued();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logExcept(['search_text']);
    }

    public function currentMembership(): ?Membership
    {
        return $this->memberships()->where('year', now('Europe/Brussels')->year)->first();
    }

    public function duesOverdue(): bool
    {
        $membership = $this->currentMembership();

        return ! $membership || $membership->status !== 'paid';
    }
}
