<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $pageTitle = ($title ?? '') . (isset($title) ? ' · ' : '') . config('app.name');
        $pageDescription = $description ?? __('Annuaire des commerçants et artisans d’Ath, agenda du centre-ville et bons plans.');
        $hreflangs = $hreflangs ?? aca_hreflangs();
        $ogImage = $ogImage ?? asset('images/hero/ath.jpg');
    @endphp
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">
    <link rel="canonical" href="{{ url()->current() }}">
    <link rel="icon" href="{{ asset('images/logo-aca.jpg') }}" type="image/jpeg">
    @foreach ($hreflangs as $lang => $url)
        <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
    @endforeach
    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @livewireStyles
    @stack('head')
</head>
<body class="min-h-screen antialiased">
    @php
        $locale = aca_locale();
        $nav = [
            ['key' => 'home', 'label' => __('Accueil')],
            ['key' => 'merchants', 'label' => __('Commerçants')],
            ['key' => 'events', 'label' => __('Agenda')],
            ['key' => 'news', 'label' => __('Actualités')],
            ['key' => 'contact', 'label' => __('Contact'), 'icon' => true],
        ];
        $path = '/'.ltrim(request()->path(), '/');
        $isHome = (bool) preg_match('#^/(fr|nl|en)/?$#', $path);
        $facebook = \App\Models\Setting::string('facebook', 'https://www.facebook.com/aca.commercantsdath/');
    @endphp

    <header class="site-header sticky top-0 z-40 border-b border-black/5">
        <div class="mx-auto flex max-w-7xl min-w-0 items-center gap-3 px-4 py-3 lg:gap-4 lg:px-8">
            <a href="{{ aca_url('home') }}" class="flex shrink-0 items-center gap-3">
                <img src="{{ asset('images/logo-aca.jpg') }}" alt="{{ __('Association des Commerçants et Artisans d’Ath') }}" class="h-14 w-14 rounded-full bg-white object-contain ring-1 ring-black/5 sm:h-20 sm:w-20">
            </a>

            <nav class="hidden lg:flex flex-1 items-center justify-center gap-4">
                @foreach ($nav as $item)
                    <a href="{{ aca_url($item['key']) }}" class="nav-link inline-flex items-center gap-1.5 {{ $item['key'] === 'home' ? ($isHome ? 'is-active' : '') : (str_contains($path, aca_path($item['key'])) ? 'is-active' : '') }}">
                        @if (! empty($item['icon']))
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        @endif
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-3">
                <div class="hidden sm:flex text-xs tracking-wider gap-1.5">
                    @foreach (config('aca.locales') as $code => $meta)
                        <a href="{{ aca_switch_locale($code) }}"
                           class="px-1 {{ $locale === $code ? 'text-plum font-semibold' : 'text-ink/50 hover:text-plum' }}">{{ $meta['short'] }}</a>
                    @endforeach
                </div>
                <a href="{{ $facebook }}" class="icon-btn header-facebook hidden lg:inline-flex" aria-label="Facebook" rel="noreferrer" target="_blank">
                    <x-facebook-icon />
                </a>
                <a href="{{ aca_url('join') }}" class="btn-gold is-white hidden sm:inline-flex text-sm py-2 px-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z"/></svg>
                    {{ __('Devenir membre') }}
                </a>
                <button type="button" class="lg:hidden p-2" x-data @click="$dispatch('open-menu')" aria-label="{{ __('Menu') }}">
                    <span class="block h-0.5 w-6 bg-ink mb-1.5"></span>
                    <span class="block h-0.5 w-6 bg-ink mb-1.5"></span>
                    <span class="block h-0.5 w-6 bg-ink"></span>
                </button>
            </div>
        </div>
    </header>

    <div x-data="{ open: false }" @open-menu.window="open = true" x-show="open" x-cloak class="menu-full fixed inset-0 z-50 overflow-y-auto p-6" style="display:none">
        <button class="absolute right-6 top-6 text-sm tracking-widest uppercase" @click="open = false">{{ __('Fermer') }}</button>
        <nav class="flex min-h-full flex-col items-center justify-center gap-5 text-2xl font-semibold uppercase tracking-[0.08em]">
            @foreach ($nav as $item)
                <a href="{{ aca_url($item['key']) }}">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ aca_url('join') }}" class="btn-gold mt-4 text-base normal-case tracking-normal">{{ __('Devenir membre') }}</a>
            <a href="{{ $facebook }}" class="mt-2 inline-flex items-center gap-3 font-sans text-base font-semibold normal-case tracking-normal text-plum" aria-label="Facebook" rel="noreferrer" target="_blank">
                <span class="icon-btn" aria-hidden="true">
                    <x-facebook-icon class="h-5 w-5" />
                </span>
                Facebook
            </a>
            <div class="flex gap-4 text-base font-sans mt-6">
                @foreach (config('aca.locales') as $code => $meta)
                    <a href="{{ aca_switch_locale($code) }}">{{ $meta['short'] }}</a>
                @endforeach
            </div>
        </nav>
    </div>

    <main>
        @if (isset($slot) && ! $slot instanceof \Illuminate\Database\Eloquent\Model)
            {{ $slot }}
        @endif
        @yield('content')
    </main>

    <footer class="bg-plum text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 md:grid-cols-4 lg:px-8">
            <div>
                <img src="{{ asset('images/logo-aca.jpg') }}" alt="" class="h-20 w-20 mb-4 rounded-full bg-white object-contain">
                <p class="font-serif text-2xl">ACA Ath</p>
                <p class="mt-2 text-sm text-white/75">{{ __('Association des Commerçants et Artisans d’Ath, depuis 1911.') }}</p>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gold mb-3">{{ __('Contact') }}</p>
                <p>Rue Ernest Cambier 2/1<br>7800 Ath</p>
                <p class="mt-2"><a class="hover:text-gold" href="mailto:president@athinfo.be">president@athinfo.be</a></p>
                <p><a class="hover:text-gold" href="tel:+32485926080">+32 485 92 60 80</a></p>
                <a href="{{ $facebook }}" class="mt-4 inline-flex h-10 w-10 items-center justify-center rounded-full border border-white/30 text-white hover:border-gold hover:bg-gold hover:text-plum" aria-label="Facebook" rel="noreferrer" target="_blank">
                    <x-facebook-icon class="h-4 w-4" />
                </a>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gold mb-3">{{ __('Explorer') }}</p>
                <ul class="space-y-1">
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('merchants') }}">{{ __('Annuaire') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('merchants') }}#carte">{{ __('Carte') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('events') }}">{{ __('Agenda') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('weekend') }}">{{ __('Week-end à Ath') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('parking') }}">{{ __('Parking & accès') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('contact') }}">{{ __('Contact') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('press') }}">{{ __('Espace presse') }}</a></li>
                </ul>
            </div>
            <div>
                <p class="text-xs uppercase tracking-[0.2em] text-gold mb-3">{{ __('Légal') }}</p>
                <ul class="space-y-1">
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('legal') }}">{{ __('Mentions légales') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('privacy') }}">{{ __('Vie privée') }}</a></li>
                    <li><a class="text-white/85 hover:text-gold" href="{{ aca_url('cookies') }}">{{ __('Cookies') }}</a></li>
                    <li class="pt-3">
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-2 rounded-full border border-white/30 px-4 py-2 text-sm text-white hover:border-gold hover:bg-gold hover:text-plum">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg>
                            {{ __('Administration') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/15">
            <div class="mx-auto flex max-w-7xl flex-col items-center px-4 py-8 text-center lg:px-8">
                <p class="text-xs uppercase tracking-[0.2em] text-gold">{{ __('Partenaires') }}</p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-4">
                    @foreach (config('aca.partners') as $partner)
                        <a href="{{ $partner['url'] }}" rel="noreferrer" target="_blank" class="rounded-xl bg-white px-5 py-3 font-serif text-lg text-plum hover:bg-gold">
                            {{ $partner['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        <p class="border-t border-white/15 py-4 text-center text-xs text-white/70 space-y-1">
            <span class="block">© {{ date('Y') }} ACA Ath · {{ __('Centre-ville d’Ath') }}</span>
            <span class="block">{{ __('Site web réalisé par') }} <a class="underline hover:text-gold" href="https://ldmedia.be" rel="noreferrer" target="_blank">LD Media</a> — {{ __('Agence de communication à Ath et Chièvres') }}</span>
        </p>
    </footer>
    <div x-data="{ show: !localStorage.getItem('aca-cookies') }" x-show="show" x-cloak
         class="fixed bottom-4 left-4 right-4 z-40 mx-auto max-w-3xl rounded-2xl bg-ink px-5 py-4 text-sm text-cream shadow-lg md:flex md:items-center md:gap-4">
        <p class="flex-1">{{ __('Nous utilisons un cookie de session pour mémoriser votre langue. Aucun traceur publicitaire.') }}
            <a class="underline" href="{{ aca_url('cookies') }}">{{ __('Cookies') }}</a>
            ·
            <a class="underline" href="{{ aca_url('legal') }}">{{ __('Mentions légales') }}</a>
            ·
            <a class="underline" href="{{ aca_url('privacy') }}">{{ __('Vie privée') }}</a>
        </p>
        <button type="button" class="btn-gold mt-3 md:mt-0" @click="localStorage.setItem('aca-cookies','1'); show = false">{{ __('J’accepte') }}</button>
    </div>
    <button
        type="button"
        class="back-to-top"
        x-data="{ show: false }"
        x-show="show"
        x-transition.opacity
        x-cloak
        @scroll.window="show = window.scrollY > 400"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        aria-label="{{ __('Retour en haut') }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75 12 8.25l7.5 7.5"/>
        </svg>
    </button>
    @livewireScripts
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    @stack('scripts')
</body>
</html>
