@extends('layouts.public')

@section('content')
@php
    $days = [1 => __('Lundi'), 2 => __('Mardi'), 3 => __('Mercredi'), 4 => __('Jeudi'), 5 => __('Vendredi'), 6 => __('Samedi'), 7 => __('Dimanche')];
    $today = now('Europe/Brussels')->isoWeekday();
    $likes = $merchant->services->map(fn ($service) => $service->t('name'))->filter()->values();
    $gallery = $merchant->getMedia('gallery');
    $socialLabels = [
        'facebook' => __('Page Facebook'),
        'instagram' => 'Instagram',
        'tiktok' => 'TikTok',
        'linkedin' => 'LinkedIn',
        'other' => __('Réseau social'),
    ];
@endphp

<section class="merchant-hero">
    <img src="{{ $merchant->cover() }}" alt="{{ $merchant->name }}">
    <div class="merchant-hero-chips">
        @foreach ($merchant->categories as $category)
            <span class="rounded-full bg-white px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-plum">{{ $category->t('name') }}</span>
        @endforeach
        <x-open-badge :status="$status" />
    </div>
</section>

<div class="merchant-body">
    <div class="mx-auto max-w-7xl px-4 lg:px-8">
        <div class="grid items-start gap-5 lg:grid-cols-[1.2fr_.8fr]">
            <div class="grid gap-5">
                <article class="sheet-card reveal">
                    <a href="{{ aca_url('merchants') }}" class="text-xs font-bold uppercase tracking-[0.16em] text-plum/70 hover:text-plum">{{ __('Annuaire') }} ←</a>
                    <h1 class="font-serif mt-2 text-4xl leading-tight md:text-5xl">{{ $merchant->name }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-sm text-ink/70">
                        <span class="inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-plum" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                            {{ $merchant->city }}
                        </span>
                        @if ($merchant->categories->isNotEmpty())
                            <span class="inline-flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" class="text-plum" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
                                {{ $merchant->categories->map->t('name')->join(' · ') }}
                            </span>
                        @endif
                    </div>
                    @if ($merchant->t('description'))
                        <p class="mt-4 text-[1.05rem] leading-relaxed text-ink">{{ $merchant->t('description') }}</p>
                    @endif
                    @if ($merchant->t('highlights'))
                        <p class="mt-3 font-medium text-plum">{{ $merchant->t('highlights') }}</p>
                    @endif
                    @if ($merchant->t('brands'))
                        <p class="mt-2 text-sm text-ink/55">{{ __('Marques') }} : {{ $merchant->t('brands') }}</p>
                    @endif
                </article>

                @if ($likes->isNotEmpty())
                    <article class="sheet-card reveal">
                        <h2 class="font-serif text-2xl">{{ __('Ce que vous aimerez') }}</h2>
                        <ul class="mt-3">
                            @foreach ($likes as $like)
                                <li class="sheet-check">
                                    <span class="sheet-check-ico" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                    </span>
                                    <span>{{ $like }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </article>
                @endif

                <article class="sheet-card reveal">
                    <h2 class="font-serif mb-3 flex items-center gap-2 text-2xl">
                        <span class="sheet-ico" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </span>
                        {{ __('Horaires') }}
                    </h2>
                    <table class="w-full text-sm">
                        @foreach ($days as $n => $label)
                            <tr class="{{ $n === $today ? 'is-today' : '' }}">
                                <td class="py-1.5 pr-3 {{ $n === $today ? 'font-semibold text-plum' : '' }}">{{ $label }}</td>
                                <td class="py-1.5 text-right {{ $n === $today ? 'font-semibold text-plum' : 'text-ink/70' }}">
                                    @forelse ($hours->get($n, collect()) as $hour)
                                        {{ substr($hour->opens_at, 0, 5) }} – {{ substr($hour->closes_at, 0, 5) }}@if (! $loop->last)<br>@endif
                                    @empty
                                        {{ __('Fermé') }}
                                    @endforelse
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </article>

                @if ($merchant->deals()->active()->exists())
                    <article class="sheet-card reveal">
                        <h2 class="font-serif mb-3 text-2xl">{{ __('Bons plans en cours') }}</h2>
                        <div class="grid gap-3">
                            @foreach ($merchant->deals()->active()->get() as $deal)
                                <div class="rounded-xl border border-gold/40 bg-gold/10 px-4 py-3">
                                    <h3 class="font-serif text-xl">{{ $deal->t('title') }}</h3>
                                    <p class="mt-1 text-sm text-ink/75">{{ $deal->t('description') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endif
            </div>

            <aside class="grid gap-5 self-start lg:sticky lg:top-28" x-data="{ suggest: false }">
                <article class="sheet-card reveal">
                    <h2 class="font-serif mb-4 text-2xl">{{ __('Contact') }}</h2>

                    <div class="mb-4 flex items-start gap-3">
                        <span class="sheet-ico" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </span>
                        <div class="min-w-0">
                            <p class="font-semibold leading-snug">{{ $merchant->address }}</p>
                            <p class="text-sm text-ink/60">{{ $merchant->postal_code }} {{ $merchant->city }}</p>
                        </div>
                    </div>

                    <div class="grid gap-2">
                        @if ($merchant->phone)
                            <a class="sheet-action is-primary" href="tel:{{ $merchant->phone }}">
                                <span class="sheet-action-ico" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                                </span>
                                {{ $merchant->phone }}
                            </a>
                        @endif

                        @if ($merchant->email)
                            <a class="sheet-action" href="mailto:{{ $merchant->email }}">
                                <span class="sheet-action-ico" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                                </span>
                                {{ $merchant->obfuscatedEmail() }}
                            </a>
                        @endif

                        <a class="sheet-action is-gold" href="{{ $merchant->directionsUrl() }}" target="_blank" rel="noreferrer">
                            <span class="sheet-action-ico" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498 4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 0 0-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0Z"/></svg>
                            </span>
                            {{ __('Itinéraire') }}
                        </a>

                        @if ($merchant->website)
                            <a class="sheet-action" href="{{ $merchant->website }}" target="_blank" rel="noreferrer">
                                <span class="sheet-action-ico" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
                                </span>
                                {{ __('Site web') }}
                            </a>
                        @endif

                        @foreach ($merchant->socialLinks as $link)
                            <a class="sheet-action" href="{{ $link->url }}" target="_blank" rel="noreferrer">
                                <span class="sheet-action-ico" aria-hidden="true">
                                    @if ($link->network === 'facebook')
                                        <x-facebook-icon class="h-4 w-4" />
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                    @endif
                                </span>
                                {{ $socialLabels[$link->network] ?? ucfirst($link->network) }}
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <button type="button" class="icon-btn" aria-label="{{ __('Partager') }}" onclick="navigator.share ? navigator.share({title: @json($merchant->name), url: location.href}) : navigator.clipboard.writeText(location.href)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z"/></svg>
                        </button>
                        <button type="button" class="icon-btn" x-data="{ fav: window.acaFavs.has({{ $merchant->id }}) }"
                                @click="fav = window.acaFavs.toggle({{ $merchant->id }}).includes({{ $merchant->id }})"
                                :aria-pressed="fav.toString()"
                                :class="fav ? 'bg-plum text-white border-plum' : ''"
                                :aria-label="fav ? '{{ __('Retirer des vitrines') }}' : '{{ __('Ajouter à mes vitrines') }}'">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" :fill="fav ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                            </svg>
                        </button>
                    </div>

                    <p class="mt-4 border-t border-black/5 pt-3">
                        <button type="button" class="text-sm font-semibold text-plum underline decoration-plum/30 underline-offset-4" @click="suggest = !suggest">{{ __('Suggérer une modification') }}</button>
                    </p>
                    <div x-show="suggest" x-cloak class="pt-4">
                        <livewire:suggest-change :merchant="$merchant" />
                    </div>
                </article>

                @if ($gallery->isNotEmpty())
                    <article class="sheet-card reveal" x-data="{ photo: null }">
                        <h2 class="font-serif mb-3 flex items-center gap-2 text-2xl">
                            <span class="sheet-ico" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z"/></svg>
                            </span>
                            {{ __('Galerie photos') }}
                        </h2>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ($gallery as $image)
                                <button type="button" class="photo-card aspect-[4/3]" @click="photo = '{{ $image->getUrl() }}'">
                                    <img src="{{ $image->getUrl() }}" alt="" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                        <button type="button" class="mt-3 w-full text-center text-sm font-semibold text-plum" @click="photo = '{{ $gallery->first()->getUrl() }}'">
                            {{ __('Voir toutes les photos') }} ({{ $gallery->count() }})
                        </button>
                        <div x-show="photo" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink/80 p-6" @click="photo = null" @keydown.escape.window="photo = null">
                            <img :src="photo" alt="" class="max-h-full max-w-full rounded-2xl shadow-2xl">
                        </div>
                    </article>
                @endif

                @if ($merchant->lat)
                    <div id="mini-map" class="reveal h-56 overflow-hidden rounded-[1.25rem] shadow-[0_10px_28px_rgba(74,28,104,0.08)]"></div>
                @endif
            </aside>
        </div>

        @if ($similar->isNotEmpty())
            <section class="mt-8">
                <h2 class="reveal font-serif mb-4 text-3xl">{{ __('Dans le même esprit') }}</h2>
                <div class="grid gap-5 {{ $similar->count() === 1 ? '' : ($similar->count() === 2 ? 'md:grid-cols-2' : 'md:grid-cols-3') }}">
                    @foreach ($similar as $item)
                        <div class="reveal">
                            <x-merchant-card :merchant="$item" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        @if ($previousMerchant && $nextMerchant)
            <section class="mt-8 grid gap-4 md:grid-cols-2">
                <a href="{{ aca_url('merchant', ['slug' => $previousMerchant->t('slug')]) }}" class="sheet-card reveal flex items-center justify-between gap-4 !p-5">
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-plum/70">{{ __('Fiche précédente') }}</p>
                        <p class="font-serif mt-1 text-2xl">{{ $previousMerchant->name }}</p>
                    </div>
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold text-2xl font-bold text-plum">←</span>
                </a>
                <a href="{{ aca_url('merchant', ['slug' => $nextMerchant->t('slug')]) }}" class="sheet-card reveal flex items-center justify-between gap-4 !p-5 text-right">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gold text-2xl font-bold text-plum">→</span>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-plum/70">{{ __('Fiche suivante') }}</p>
                        <p class="font-serif mt-1 text-2xl">{{ $nextMerchant->name }}</p>
                    </div>
                </a>
            </section>
        @endif
    </div>
</div>

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
