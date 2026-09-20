<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NominatimGeocoder
{
    public function geocode(string $address): ?array
    {
        if (trim($address) === '') {
            return null;
        }

        try {
            $response = Http::timeout(8)
                ->withHeaders(['User-Agent' => 'ACA-Ath-Directory/1.0 (info@ldmedia.be)'])
                ->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $address,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'be',
                ]);

            if (! $response->ok()) {
                return null;
            }

            $first = $response->json()[0] ?? null;
            if (! $first) {
                return null;
            }

            return [
                'lat' => (float) $first['lat'],
                'lng' => (float) $first['lon'],
            ];
        } catch (\Throwable $e) {
            Log::warning('Geocoding failed', ['address' => $address, 'error' => $e->getMessage()]);

            return null;
        }
    }
}
