<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class DeepLTranslator
{
    public function enabled(): bool
    {
        return filled(config('services.deepl.key'));
    }

    public function translate(string $text, string $target): string
    {
        if (! $this->enabled() || trim($text) === '') {
            return $text;
        }

        $response = Http::asForm()->post(config('services.deepl.endpoint'), [
            'auth_key' => config('services.deepl.key'),
            'text' => $text,
            'source_lang' => 'FR',
            'target_lang' => strtoupper($target === 'en' ? 'EN-GB' : $target),
        ]);

        if (! $response->successful()) {
            return $text;
        }

        return (string) data_get($response->json(), 'translations.0.text', $text);
    }
}
