<div
    x-data
    x-init="
        if (new URLSearchParams(location.search).get('mes') === '1') {
            $wire.showFavorites(window.acaFavs.ids());
        }
    "
>
    <section class="mx-auto max-w-7xl px-4 pt-10 pb-4 lg:px-8">
        <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Annuaire') }}</p>
        <h1 class="font-serif text-4xl mt-2 md:text-5xl">{{ __('Les membres de l’ACA') }}</h1>
        <p class="mt-3 max-w-2xl text-ink/70">{{ $merchants->total() }} {{ __('adresses à parcourir, filtrer, partager.') }}</p>
    </section>

    <div class="directory-filters">
        <div class="mx-auto max-w-7xl px-4 py-4 lg:px-8">
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="$set('category', null)" class="pill {{ !$category ? 'is-active' : '' }}">{{ __('Tout afficher') }}</button>
                @foreach ($categories as $cat)
                    <button type="button" wire:click="$set('category', '{{ $cat->t('slug') }}')" class="pill {{ $category === $cat->t('slug') ? 'is-active' : '' }}">{{ $cat->t('name') }}</button>
                @endforeach
            </div>

            <div class="mt-4 grid gap-4 md:grid-cols-[1fr_auto] items-end">
                <input type="search" wire:model.live.debounce.300ms="q" placeholder="{{ __('Nom, activité, marque…') }}" class="w-full rounded-full border border-plum/20 bg-white px-5 py-3">
                <div class="flex flex-wrap gap-2 text-sm">
                    <label class="pill cursor-pointer {{ $open ? 'is-active' : '' }}"><input type="checkbox" wire:model.live="open" class="sr-only"> {{ __('Ouvert maintenant') }}</label>
                    <label class="pill cursor-pointer {{ $sunday ? 'is-active' : '' }}"><input type="checkbox" wire:model.live="sunday" class="sr-only"> {{ __('Ouvert le dimanche') }}</label>
                    <button type="button" class="pill {{ $sort === 'near' ? 'is-active' : '' }}" @click="navigator.geolocation.getCurrentPosition(p => $wire.setLocation(p.coords.latitude, p.coords.longitude))">{{ __('Près de moi') }}</button>
                    <button type="button" class="pill {{ $favoritesOnly ? 'is-active' : '' }}" @click="$wire.showFavorites(window.acaFavs.ids())">{{ __('Mes vitrines') }}</button>
                    <select wire:model.live="sort" class="rounded-full border border-plum/20 bg-white px-3 py-2 text-sm">
                        <option value="az">A → Z</option>
                        <option value="new">{{ __('Nouveaux membres') }}</option>
                        <option value="random">{{ __('Aléatoire') }}</option>
                        @if ($userLat)
                            <option value="near">{{ __('Distance') }}</option>
                        @endif
                    </select>
                </div>
            </div>

            <div class="mt-3 flex flex-wrap gap-2 text-sm">
                @foreach ($allServices as $service)
                    <label class="pill cursor-pointer {{ in_array($service->key, $services) ? 'is-active' : '' }}">
                        <input type="checkbox" wire:model.live="services" value="{{ $service->key }}" class="sr-only">
                        {{ $service->t('name') }}
                    </label>
                @endforeach
            </div>

            @if ($q !== '' || $category || $open || $sunday || $services || $favoritesOnly || $sort === 'near')
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    @if ($q !== '')
                        <button type="button" wire:click="clearSearch" class="pill is-active">« {{ $q }} » ×</button>
                    @endif
                    @if ($activeCategory)
                        <button type="button" wire:click="clearCategory" class="pill is-active">{{ $activeCategory->t('name') }} ×</button>
                    @endif
                    @if ($open)
                        <button type="button" wire:click="$set('open', false)" class="pill is-active">{{ __('Ouvert maintenant') }} ×</button>
                    @endif
                    @if ($sunday)
                        <button type="button" wire:click="$set('sunday', false)" class="pill is-active">{{ __('Ouvert le dimanche') }} ×</button>
                    @endif
                    @foreach ($allServices->whereIn('key', $services) as $service)
                        <button type="button" wire:click="clearService('{{ $service->key }}')" class="pill is-active">{{ $service->t('name') }} ×</button>
                    @endforeach
                    @if ($favoritesOnly)
                        <button type="button" wire:click="clearFavorites" class="pill is-active">{{ __('Mes vitrines') }} ×</button>
                    @endif
                    @if ($sort === 'near')
                        <button type="button" wire:click="clearNear" class="pill is-active">{{ __('Près de moi') }} ×</button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <section id="carte" class="mx-auto max-w-7xl px-4 py-8 lg:px-8">
        <div class="directory-map photo-card bg-mist">
            <div id="aca-map" wire:ignore class="h-full w-full"></div>
        </div>
        <script type="application/json" id="dir-points">@json($points)</script>
        <script type="application/json" id="dir-selected">@json($selected)</script>
    </section>

    <section id="annuaire-liste" class="mx-auto max-w-7xl px-4 pb-12 lg:px-8">
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($merchants as $merchant)
                <x-merchant-card :merchant="$merchant" :status="$opening->for($merchant)" :selected="$selected === $merchant->id" />
            @empty
                <p class="col-span-full">{{ __('Aucun commerce ne correspond à votre recherche.') }}</p>
            @endforelse
        </div>
        @if ($merchants->hasMorePages())
            <div class="mt-8 flex justify-center">
                <button type="button" class="btn-gold" wire:click="loadMore">{{ __('Charger plus') }}</button>
            </div>
        @endif
        @if ($merchants->hasPages())
            <div class="mt-6">
                {{ $merchants->onEachSide(1)->links('pagination.aca') }}
            </div>
        @endif
    </section>
</div>

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    window.acaDirectory = window.acaDirectory || { map: null, markers: {} };
    function acaReadPoints() {
        const el = document.getElementById('dir-points');
        return el ? JSON.parse(el.textContent || '[]') : [];
    }
    window.acaPulsePin = function (id) {
        const marker = window.acaDirectory.markers?.[id];
        if (!marker || !window.acaDirectory.map) return;
        const el = marker.getElement();
        if (el) {
            el.classList.add('is-pulsing');
            setTimeout(() => el.classList.remove('is-pulsing'), 900);
        }
        const base = marker.options.radius || 7;
        marker.setRadius(base + 6);
        setTimeout(() => marker.setRadius(base), 450);
        window.acaDirectory.map.panTo(marker.getLatLng());
        marker.openPopup();
    };
    function acaFocusCard(id) {
        const card = document.getElementById('merchant-' + id);
        document.querySelectorAll('[data-merchant-id].is-selected').forEach((el) => el.classList.remove('is-selected'));
        if (card) {
            card.classList.add('is-selected');
            card.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    function acaDrawDirectoryMap(points, selectedId) {
        const el = document.getElementById('aca-map');
        if (!el || typeof L === 'undefined') return;
        if (!window.acaDirectory.map) {
            window.acaDirectory.map = L.map(el).setView([50.6305, 3.778], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(window.acaDirectory.map);
        }
        Object.values(window.acaDirectory.markers).forEach((m) => window.acaDirectory.map.removeLayer(m));
        window.acaDirectory.markers = {};
        const bounds = [];
        (points || []).forEach((p) => {
            const marker = L.circleMarker([p.lat, p.lng], {
                radius: p.id === selectedId ? 10 : 7,
                color: p.color || '#6B2B91',
                fillOpacity: 0.95,
                weight: 2,
            }).addTo(window.acaDirectory.map);
            marker.bindPopup(
                `<strong>${p.name}</strong><br>${p.address || ''}<br><a href="${p.url}">{{ __('Voir la fiche') }}</a> · <a href="${p.directions}" target="_blank" rel="noreferrer">{{ __('Itinéraire') }}</a>`
            );
            marker.on('click', () => acaFocusCard(p.id));
            window.acaDirectory.markers[p.id] = marker;
            bounds.push([p.lat, p.lng]);
        });
        const applyBounds = () => {
            window.acaDirectory.map.invalidateSize();
            if (bounds.length > 1) {
                window.acaDirectory.map.fitBounds(bounds, { padding: [28, 28], maxZoom: 16 });
            } else if (bounds.length === 1) {
                window.acaDirectory.map.setView(bounds[0], 16);
            }
        };
        applyBounds();
        setTimeout(applyBounds, 200);
        setTimeout(applyBounds, 600);
        if (selectedId) {
            window.acaPulsePin(selectedId);
        }
    }
    document.addEventListener('DOMContentLoaded', () => acaDrawDirectoryMap(acaReadPoints(), JSON.parse(document.getElementById('dir-selected')?.textContent || 'null')));
    document.addEventListener('livewire:init', () => {
        Livewire.on('refreshMap', () => setTimeout(() => acaDrawDirectoryMap(acaReadPoints(), JSON.parse(document.getElementById('dir-selected')?.textContent || 'null')), 30));
        Livewire.on('pulse-pin', (payload) => {
            const id = payload?.id ?? payload?.[0]?.id;
            if (id) {
                setTimeout(() => window.acaPulsePin(id), 20);
            }
        });
    });
</script>
@endpush
