<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['name'];

    protected $fillable = ['key', 'name', 'icon', 'position'];

    public function merchants(): BelongsToMany
    {
        return $this->belongsToMany(Merchant::class);
    }
}
