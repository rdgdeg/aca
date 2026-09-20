<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['name', 'slug'];

    protected $fillable = [
        'parent_id', 'name', 'slug', 'icon', 'pin_color', 'cover_url', 'position',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('position');
    }

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class);
    }

    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id')->orderBy('position');
    }
}
