<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('aca.settings', 60, function () {
            return static::query()->pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget('aca.settings');
    }

    public static function string(string $key, string $default = ''): string
    {
        $value = static::get($key, $default);

        if (is_array($value)) {
            return (string) ($value[aca_locale()] ?? $value['fr'] ?? $default);
        }

        return (string) ($value ?? $default);
    }
}
