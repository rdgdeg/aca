@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 pt-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Parking & accès') }}</p>
    <h1 class="font-serif text-4xl mt-2">{{ __('Venir en ville, sans le casse-tête') }}</h1>
    <p class="mt-4 max-w-2xl text-ink/70">{{ __('Garez-vous, marchez cinq minutes, et le centre-ville est à vous. Un tap ouvre l’itinéraire.') }}</p>
</section>

<section class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
    <div class="directory-map photo-card bg-mist">
        <div id="parking-map" class="h-full w-full"></div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 pb-16 lg:px-8">
    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        @foreach ($spots as $spot)
            @php
                $query = urlencode($spot['name'].' Ath');
                $maps = 'https://www.google.com/maps/dir/?api=1&destination='.$query;
                $agent = (string) request()->userAgent();
                if (preg_match('/iPhone|iPad|Macintosh/', $agent) === 1) {
                    $maps = 'https://maps.apple.com/?daddr='.$query;
                }
            @endphp
            <article class="rounded-3xl bg-white p-5 shadow-sm">
                <h2 class="font-serif text-2xl">{{ $spot['name'] }}</h2>
                <p class="mt-2 text-sm text-ink/70">{{ $spot['hint'] }}</p>
                <a href="{{ $maps }}" class="btn-plum mt-5 inline-flex text-sm py-2 px-4" target="_blank" rel="noreferrer">{{ __('Itinéraire') }} →</a>
            </article>
        @endforeach
    </div>
    <p class="mt-10 text-sm text-ink/60">{{ __('Gare SNCB Ath à 10 minutes à pied du cœur de ville. Bus TEC : arrêt Ath Gare / Ath Centre.') }}</p>
</section>
@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const spots = @json($spots);
    const map = L.map('parking-map').setView([50.6305, 3.778], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(map);
    const bounds = [];
    spots.forEach((spot) => {
        const marker = L.circleMarker([spot.lat, spot.lng], { radius: 8, color: '#6B2B91', fillOpacity: 0.95, weight: 2 }).addTo(map);
        marker.bindPopup(`<strong>${spot.name}</strong><br>${spot.hint}`);
        bounds.push([spot.lat, spot.lng]);
    });
    if (bounds.length) {
        map.fitBounds(bounds, { padding: [28, 28], maxZoom: 16 });
    }
</script>
@endpush
