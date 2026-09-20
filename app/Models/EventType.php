<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class EventType extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['name'];

    protected $fillable = ['name', 'color', 'slug'];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
