<?php

namespace App\Models;

use App\Support\TranslatableFallback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasTranslations, TranslatableFallback;

    public array $translatable = ['title', 'success_message', 'ack_subject', 'ack_body'];

    protected $fillable = [
        'name', 'title', 'system_key', 'recipients', 'success_message',
        'redirect_url', 'ack_enabled', 'ack_subject', 'ack_body', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'recipients' => 'array',
            'ack_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function fields(): HasMany
    {
        return $this->hasMany(FormField::class)->orderBy('position');
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public static function system(string $key): ?self
    {
        return static::query()->where('system_key', $key)->where('is_active', true)->first();
    }
}
