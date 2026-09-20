<?php

namespace App\Support;

trait TranslatableFallback
{
    public function t(string $attribute, ?string $locale = null): string
    {
        $locale ??= app()->getLocale();
        $translations = $this->getTranslations($attribute);
        $value = $translations[$locale] ?? $translations['fr'] ?? $translations[config('aca.fallback_locale', 'fr')] ?? '';

        return is_string($value) ? $value : '';
    }

    public function translationMissing(string $attribute, ?string $locale = null): bool
    {
        $locale ??= app()->getLocale();
        $translations = $this->getTranslations($attribute);
        $value = $translations[$locale] ?? null;

        return ! is_string($value) || trim($value) === '';
    }
}
