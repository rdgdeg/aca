@extends('layouts.public')

@section('content')
@php
    $days = [1 => __('Lundi'), 2 => __('Mardi'), 3 => __('Mercredi'), 4 => __('Jeudi'), 5 => __('Vendredi'), 6 => __('Samedi'), 7 => __('Dimanche')];
    $today = now('Europe/Brussels')->isoWeekday();
@endphp
<section class="merchant-hero relative">
    <img src="{{ $merchant->cover() }}" alt="{{ $merchant->name }}" class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-cream via-cream/30 to-black/10"></div>
    <div class="absolute bottom-0 left-0 right-0 mx-auto max-w-7xl px-4 pb-6 lg:px-8">
        <p class="text-plum text-sm">{{ $merchant->categories->map->t('name')->join(' · ') }}</p>
        <h1 class="font-serif text-4xl md:text-5xl">{{ $merchant->name }}</h1>
        <div class="mt-2"><x-open-badge :status="$status" /></div>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 lg:px-8 grid gap-10 lg:grid-cols-[1.15fr_.85fr]">
    <div>
        <p class="text-lg leading-relaxed">{{ $merchant->t('description') }}</p>
        @if ($merchant->t('highlights'))
            <p class="mt-4 text-plum">{{ $merchant->t('highlights') }}</p>
        @endif
        @if ($merchant->t('brands'))
            <p class="mt-2 text-sm text-ink/60">{{ __('Marques') }} : {{ $merchant->t('brands') }}</p>
        @endif
        <div class="mt-6 flex flex-wrap gap-2">
            @foreach ($merchant->services as $service)
                <span class="pill">{{ $service->t('name') }}</span>
            @endforeach
        </div>

        @if ($merchant->getMedia('gallery')->isNotEmpty())
            <div class="mt-10 grid grid-cols-2 md:grid-cols-3 gap-3">
                @foreach ($merchant->getMedia('gallery') as $image)
                    <a href="{{ $image->getUrl() }}" target="_blank" class="photo-card aspect-square block">
                        <img src="{{ $image->getUrl() }}" alt="" class="h-full w-full object-cover">
                    </a>
                @endforeach
            </div>
        @endif

        <div class="mt-8 flex flex-wrap items-center gap-2">
            @if ($merchant->phone)
                <a class="icon-btn" href="tel:{{ $merchant->phone }}" aria-label="{{ __('Appeler') }}" title="{{ $merchant->phone }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                </a>
            @endif
            @if ($merchant->email)
                <a class="icon-btn" href="mailto:{{ $merchant->email }}" aria-label="{{ __('Email') }}" title="{{ $merchant->email }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </a>
            @endif
            <a class="icon-btn" href="{{ $merchant->directionsUrl() }}" target="_blank" rel="noreferrer" aria-label="{{ __('Itinéraire') }}" title="{{ __('Itinéraire') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
            </a>
            @if ($merchant->website)
                <a class="icon-btn" href="{{ $merchant->website }}" target="_blank" rel="noreferrer" aria-label="{{ __('Site web') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                </a>
            @endif
            <button type="button" class="icon-btn" aria-label="{{ __('Partager') }}" onclick="navigator.share ? navigator.share({title: @json($merchant->name), url: location.href}) : navigator.clipboard.writeText(location.href)">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
            </button>
            <button type="button" class="icon-btn" x-data="{ fav: window.acaFavs.has({{ $merchant->id }}) }"
                    @click="fav = window.acaFavs.toggle({{ $merchant->id }}).includes({{ $merchant->id }})"
                    :aria-pressed="fav.toString()"
                    :class="fav ? 'bg-plum text-white border-plum' : ''">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" :fill="fav ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                </svg>
            </button>
            <a href="{{ $merchant->directionsUrl() }}" class="btn-gold text-sm py-2 px-4" target="_blank" rel="noreferrer">{{ __('Itinéraire') }} →</a>
        </div>
    </div>

    <aside class="space-y-5 lg:sticky lg:top-28 self-start" x-data="{ suggest: false }">
        <div class="rounded-2xl bg-white p-6">
            <h2 class="font-serif text-2xl mb-4">{{ __('Horaires') }}</h2>
            <table class="w-full text-sm">
                @foreach ($days as $n => $label)
                    <tr class="{{ $n === $today ? 'font-semibold text-plum' : '' }}">
                        <td class="py-1.5">{{ $label }}</td>
                        <td class="py-1.5 text-right">
                            @forelse ($hours->get($n, collect()) as $hour)
                                {{ substr($hour->opens_at, 0, 5) }} – {{ substr($hour->closes_at, 0, 5) }}@if (!$loop->last)<br>@endif
                            @empty
                                {{ __('Fermé') }}
                            @endforelse
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
        <div class="rounded-2xl bg-white p-6 space-y-2 text-sm">
            <p>{{ $merchant->address }}<br>{{ $merchant->postal_code }} {{ $merchant->city }}</p>
            @if ($merchant->phone)<p><a href="tel:{{ $merchant->phone }}">{{ $merchant->phone }}</a></p>@endif
            @if ($merchant->email)<p>{{ $merchant->obfuscatedEmail() }}</p>@endif
            @if ($merchant->website)<p><a href="{{ $merchant->website }}" rel="noreferrer" target="_blank">{{ __('Site web') }}</a></p>@endif
            <div class="flex gap-3 pt-2">
                @foreach ($merchant->socialLinks as $link)
                    <a href="{{ $link->url }}" class="capitalize" target="_blank" rel="noreferrer">{{ $link->network }}</a>
                @endforeach
            </div>
            <p class="pt-3">
                <button type="button" class="text-plum underline" @click="suggest = !suggest">{{ __('Suggérer une modification') }}</button>
            </p>
            <div x-show="suggest" x-cloak class="pt-4 border-t border-black/5">
                <livewire:suggest-change :merchant="$merchant" />
            </div>
        </div>
        @if ($merchant->lat)
            <div id="mini-map" class="h-56 rounded-2xl overflow-hidden"></div>
        @endif
    </aside>
</section>

@if ($merchant->deals()->active()->exists())
<section class="mx-auto max-w-7xl px-4 pb-10 lg:px-8">
    <h2 class="font-serif text-3xl mb-4">{{ __('Bons plans en cours') }}</h2>
    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($merchant->deals()->active()->get() as $deal)
            <div class="rounded-2xl bg-white p-5 border border-gold/30">
                <h3 class="font-serif text-xl">{{ $deal->t('title') }}</h3>
                <p class="text-sm mt-2">{{ $deal->t('description') }}</p>
            </div>
        @endforeach
    </div>
</section>
@endif

@if ($similar->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 pb-10 lg:px-8">
    <h2 class="font-serif text-3xl mb-6">{{ __('Dans le même esprit') }}</h2>
    <div class="grid md:grid-cols-3 gap-6">
        @foreach ($similar as $item)
            <x-merchant-card :merchant="$item" />
        @endforeach
    </div>
</section>
@endif

@if ($previousMerchant && $nextMerchant)
<section class="mx-auto max-w-7xl px-4 pb-16 lg:px-8">
    <div class="grid gap-4 md:grid-cols-2">
        <a href="{{ aca_url('merchant', ['slug' => $previousMerchant->t('slug')]) }}" class="flex items-center justify-between gap-4 rounded-2xl bg-white px-5 py-5 shadow-sm">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-plum/70">{{ __('Fiche précédente') }}</p>
                <p class="mt-1 font-serif text-2xl">{{ $previousMerchant->name }}</p>
            </div>
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold text-2xl font-bold text-plum">←</span>
        </a>
        <a href="{{ aca_url('merchant', ['slug' => $nextMerchant->t('slug')]) }}" class="flex items-center justify-between gap-4 rounded-2xl bg-white px-5 py-5 text-right shadow-sm">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold text-2xl font-bold text-plum">→</span>
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-plum/70">{{ __('Fiche suivante') }}</p>
                <p class="mt-1 font-serif text-2xl">{{ $nextMerchant->name }}</p>
            </div>
        </a>
    </div>
</section>
@endif

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'LocalBusiness',
    'name' => $merchant->name,
    'description' => $merchant->t('short_text'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => $merchant->address,
        'postalCode' => $merchant->postal_code,
        'addressLocality' => $merchant->city,
        'addressCountry' => 'BE',
    ],
    'telephone' => $merchant->phone,
    'url' => url()->current(),
    'image' => $merchant->cover(),
    'geo' => $merchant->lat ? ['@type' => 'GeoCoordinates', 'latitude' => $merchant->lat, 'longitude' => $merchant->lng] : null,
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection

@if ($merchant->lat)
@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const mini = L.map('mini-map').setView([{{ $merchant->lat }}, {{ $merchant->lng }}], 16);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(mini);
    L.marker([{{ $merchant->lat }}, {{ $merchant->lng }}]).addTo(mini)
        .bindPopup(`<a href="{{ $merchant->directionsUrl() }}" target="_blank" rel="noreferrer">{{ __('Itinéraire') }}</a>`);
</script>
@endpush
@endif
