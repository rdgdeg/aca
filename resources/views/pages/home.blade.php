@extends('layouts.public')

@section('content')
@php
    $hero = \App\Models\Setting::string('hero_image', 'https://images.unsplash.com/photo-1467269204594-9661b134dd2b?auto=format&fit=crop&w=2000&q=80');
    $agendaJson = $events->map(fn ($event) => [
        'title' => $event->t('title'),
        'day' => optional($event->starts_at)->format('d'),
        'month' => optional($event->starts_at)->translatedFormat('M'),
        'location' => $event->location,
        'url' => aca_url('event', ['slug' => $event->t('slug')]),
        'cover' => $event->cover() ?: asset('images/hero/ath.jpg'),
    ])->values();
@endphp
<section class="hero relative flex items-center" style="background-image: url('{{ $hero }}')">
    <div class="relative z-10 mx-auto w-full max-w-4xl px-4 py-16 text-center lg:px-8">
        <p class="text-white/80 text-xs uppercase tracking-[0.35em]">Ath · Centre-ville</p>
        <h1 class="mt-4 font-serif text-5xl text-white md:text-6xl">{{ __('Le commerce athois, à ciel ouvert.') }}</h1>
        <form action="{{ aca_url('merchants') }}" method="get" class="search-bar mx-auto mt-8 flex max-w-2xl items-center gap-3 px-2 py-2">
            <input type="search" name="q" placeholder="{{ __('Que cherchez-vous ?') }}" class="flex-1 rounded-full px-5 py-3 outline-none bg-transparent text-left">
            <button class="btn-plum">{{ __('Rechercher') }}</button>
        </form>
        <div class="mt-5 flex flex-wrap justify-center gap-2">
            <a href="{{ aca_url('weekend') }}" class="btn-gold text-sm py-2 px-4">{{ __('Week-end à Ath') }}</a>
            <a href="{{ aca_url('parking') }}" class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-plum">{{ __('Parking & accès') }}</a>
            <a href="{{ aca_url('merchants') }}?mes=1" class="rounded-full bg-white/90 px-4 py-2 text-sm font-semibold text-plum">{{ __('Mes vitrines') }}</a>
        </div>
    </div>
</section>

@if ($agendaJson->isNotEmpty())
<section class="band-alt relative" x-data="{ events: {{ $agendaJson->toJson() }}, i: 0 }" x-cloak>
    <div class="relative mx-auto max-w-7xl px-4 py-14 lg:px-8">
        <div class="mb-8 grid gap-6 lg:grid-cols-[1fr_1.1fr] lg:items-end">
            <div>
                <span class="mark-bar bg-gold"></span>
                <h2 class="font-serif text-4xl text-plum md:text-6xl">{{ __('À vos agendas !') }}</h2>
            </div>
            <div class="lg:text-right">
                <p class="text-sm text-ink/70 lg:ml-auto lg:max-w-md">{{ __('Entre traditions, animations de centre-ville et vitrines ouvertes : il y a toujours un bon motif pour venir à Ath.') }}</p>
                <a href="{{ aca_url('events') }}" class="btn-gold mt-4">{{ __('Tous les événements') }} →</a>
            </div>
        </div>

        <div class="relative px-16 md:px-20">
            <button type="button" class="agenda-arrow is-left" @click="i = (i + events.length - 1) % events.length" aria-label="{{ __('Précédent') }}">‹</button>
            <button type="button" class="agenda-arrow is-right" @click="i = (i + 1) % events.length" aria-label="{{ __('Suivant') }}">›</button>
            <div class="grid gap-4 sm:grid-cols-3">
                <template x-for="offset in [0, 1, 2]" :key="offset">
                    <a :href="events[(i + offset) % events.length].url" class="listing-card overflow-hidden" x-show="events.length > offset">
                        <div class="relative aspect-[16/10]">
                            <img :src="events[(i + offset) % events.length].cover" alt="" class="absolute inset-0 h-full w-full object-cover">
                            <div class="agenda-date absolute left-3 top-3">
                                <span class="text-sm font-extrabold" x-text="events[(i + offset) % events.length].day"></span>
                                <span class="mt-1 text-[9px] font-bold uppercase tracking-wider" x-text="events[(i + offset) % events.length].month"></span>
                            </div>
                        </div>
                        <div class="p-4">
                            <p class="font-serif text-2xl leading-tight" x-text="events[(i + offset) % events.length].title"></p>
                            <p class="mt-2 flex items-center gap-1 text-xs text-ink/55">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                <span x-text="events[(i + offset) % events.length].location"></span>
                            </p>
                        </div>
                    </a>
                </template>
            </div>
        </div>
    </div>
</section>
@endif

<section class="band-white">
    <div class="mx-auto max-w-7xl px-4 section-space lg:px-8 grid gap-12 lg:grid-cols-2 items-center">
        <div class="reveal">
            <span class="mark-bar"></span>
            <p class="section-kicker">{{ __('L’association') }}</p>
            <h2 class="font-serif text-4xl md:text-5xl mt-3">{{ __('Depuis 1911, le commerce athois.') }}</h2>
            <p class="mt-5 text-ink/75 max-w-xl">{{ __('L’Association des Commerçants et Artisans d’Ath anime le centre-ville, défend les vitrines indépendantes et rassemble les membres autour de la Grand-Place, de la Ducasse et des rendez-vous de l’année.') }}</p>
            <a href="{{ aca_url('contact') }}" class="btn-plum mt-7">{{ __('Découvrir l’ACA') }}</a>
        </div>
        <div class="reveal grid grid-cols-2 gap-3" style="transition-delay: .12s">
            <img src="{{ asset('images/hero/ath.jpg') }}" alt="" class="col-span-2 h-52 w-full rounded-2xl object-cover">
            <img src="{{ $featured->get(0)?->cover() ?? asset('images/shops/1.jpg') }}" alt="" class="h-36 w-full rounded-2xl object-cover">
            <img src="{{ $featured->get(1)?->cover() ?? asset('images/shops/2.jpg') }}" alt="" class="h-36 w-full rounded-2xl object-cover">
        </div>
    </div>
</section>

<section class="band-alt">
    <div class="mx-auto max-w-7xl px-4 section-space lg:px-8">
        <div class="reveal mb-10 flex items-end justify-between gap-4">
            <div>
                <span class="mark-bar"></span>
                <p class="section-kicker">{{ __('Annuaire') }}</p>
                <h2 class="font-serif text-4xl md:text-5xl mt-2">{{ __('Membres de l’ACA') }}</h2>
            </div>
            <a href="{{ aca_url('merchants') }}" class="text-sm font-semibold text-plum">{{ __('Tout l’annuaire') }} →</a>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($featured as $i => $merchant)
                <div class="reveal" style="transition-delay: {{ $i * 70 }}ms">
                    <x-merchant-card :merchant="$merchant" />
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="agenda-band">
    <div class="mx-auto max-w-7xl px-4 section-space lg:px-8">
        <div class="grid gap-8 lg:grid-cols-[.9fr_1.1fr] lg:items-stretch">
            <div class="reveal flex flex-col justify-between">
                <div>
                    <span class="mark-bar bg-gold"></span>
                    <p class="section-kicker text-gold">ACA</p>
                    <h2 class="font-serif text-4xl text-white md:text-5xl mt-2">{{ __('Rejoindre l’association') }}</h2>
                    <p class="mt-4 max-w-md text-white/80">{{ __('Envoyez votre demande : le comité valide, puis crée la fiche.') }}</p>
                </div>
                <ul class="mt-8 space-y-4">
                    <li class="flex gap-4 rounded-2xl bg-white/10 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold text-sm font-extrabold text-plum">1</span>
                        <div>
                            <p class="font-semibold text-white">{{ __('Une fiche dans l’annuaire') }}</p>
                            <p class="text-sm text-white/70">{{ __('Photo, horaires, carte et contacts, visibles en français, néerlandais et anglais.') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-2xl bg-white/10 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold text-sm font-extrabold text-plum">2</span>
                        <div>
                            <p class="font-semibold text-white">{{ __('L’agenda partagé') }}</p>
                            <p class="text-sm text-white/70">{{ __('Vos rendez-vous et ceux du centre-ville, au même endroit.') }}</p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-2xl bg-white/10 p-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold text-sm font-extrabold text-plum">3</span>
                        <div>
                            <p class="font-semibold text-white">{{ __('Une voix collective') }}</p>
                            <p class="text-sm text-white/70">{{ __('L’ACA défend les vitrines indépendantes autour de la Grand-Place.') }}</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="reveal rounded-3xl bg-white p-6 text-ink shadow-lg md:p-8">
                <p class="font-serif text-2xl">{{ __('Devenir membre') }}</p>
                <p class="mt-1 text-sm text-ink/60">{{ __('Réponse du comité sous peu.') }}</p>
                @if ($joinForm)
                    <div class="mt-5">
                        <livewire:public-form :form="$joinForm" :key="'home-join-'.$joinForm->id" />
                    </div>
                @else
                    <a href="{{ aca_url('join') }}" class="btn-gold mt-6">{{ __('Devenir membre') }}</a>
                @endif
            </div>
        </div>
    </div>
</section>

<section class="band-white relative overflow-hidden">
    <div class="mx-auto max-w-6xl px-4 section-space lg:px-8">
        <div class="reveal mb-10 text-center">
            <span class="mark-bar mx-auto"></span>
            <p class="section-kicker">{{ __('Actualités') }}</p>
            <h2 class="font-serif text-4xl md:text-5xl mt-2">{{ __('La vie de l’association') }}</h2>
        </div>
        <div class="relative px-4 py-10 md:px-10 md:py-14">
            <div class="absolute inset-y-4 left-8 right-8 rounded-[2.5rem] bg-gold/40 md:inset-y-6 md:left-16 md:right-16"></div>
            <div class="relative grid gap-6 md:grid-cols-3">
                @foreach ($posts as $post)
                    <article class="listing-card reveal">
                        <img src="{{ $post->cover() }}" alt="" class="aspect-[16/10] w-full object-cover">
                        <div class="flex flex-1 flex-col p-5">
                            <p class="text-xs text-ink/50">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                            <h3 class="font-serif text-2xl mt-1">{{ $post->t('title') }}</h3>
                            <p class="text-sm text-ink/70 mt-2 line-clamp-3">{{ $post->t('excerpt') }}</p>
                            <a href="{{ aca_url('post', ['slug' => $post->t('slug')]) }}" class="mt-auto pt-4 text-sm font-medium text-plum/70 hover:text-plum">{{ __('Lire la fiche') }} →</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'ACA Ath',
    'url' => url('/fr'),
    'logo' => asset('images/logo-aca.jpg'),
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Rue Ernest Cambier 2/1',
        'postalCode' => '7800',
        'addressLocality' => 'Ath',
        'addressCountry' => 'BE',
    ],
    'email' => 'president@athinfo.be',
    'telephone' => '+32485926080',
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
