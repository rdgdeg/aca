@php
    $status = $status ?? app(\App\Services\OpeningStatus::class)->for($merchant);
    $badgeClass = match ($status['key']) {
        'open' => 'badge-open',
        'closing_soon' => 'badge-soon',
        default => 'badge-closed',
    };
    $category = $merchant->categories->first();
    $selected = $selected ?? false;
    $distance = $merchant->near_km ?? null;
    $summary = $merchant->t('short_text') ?: $merchant->t('highlights');
@endphp
<article
    id="merchant-{{ $merchant->id }}"
    data-merchant-id="{{ $merchant->id }}"
    class="listing-card {{ $class ?? '' }} {{ $selected ? 'is-selected' : '' }}"
    x-data="{ fav: window.acaFavs.has({{ $merchant->id }}) }"
    @click="window.acaSelectMerchant({{ $merchant->id }})"
>
    <a href="{{ aca_url('merchant', ['slug' => $merchant->t('slug')]) }}" class="relative block aspect-[16/10] overflow-hidden" @click.stop>
        <img src="{{ $merchant->cover() }}" alt="{{ $merchant->name }}" class="h-full w-full object-cover" loading="lazy">
        @if ($category)
            <span class="cat-chip">{{ $category->t('name') }}</span>
        @endif
        <span class="absolute left-3 top-3 rounded-full px-3 py-1 text-xs font-semibold {{ $badgeClass }}">{{ $status['label'] }}</span>
    </a>
    <div class="flex flex-1 flex-col p-5">
        <div class="flex items-start justify-between gap-3">
            <h3 class="font-serif text-2xl leading-tight">
                <a href="{{ aca_url('merchant', ['slug' => $merchant->t('slug')]) }}" class="hover:text-plum" @click.stop>{{ $merchant->name }}</a>
            </h3>
            <button type="button"
                    class="mt-1 shrink-0 text-plum"
                    @click.stop="fav = window.acaFavs.toggle({{ $merchant->id }}).includes({{ $merchant->id }})"
                    :aria-pressed="fav.toString()"
                    :aria-label="fav ? '{{ __('Retirer des vitrines') }}' : '{{ __('Ajouter à mes vitrines') }}'">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" :fill="fav ? 'currentColor' : 'none'" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"/>
                </svg>
            </button>
        </div>
        <p class="mt-2 text-sm text-ink/60">{{ $merchant->address }}, {{ $merchant->postal_code }} {{ $merchant->city }}</p>
        @if ($distance !== null)
            <p class="mt-1 text-xs font-semibold text-plum">{{ __('À :km km', ['km' => $distance]) }}</p>
        @endif
        @if ($summary)
            <p class="mt-3 text-sm leading-relaxed text-ink/75">{{ \Illuminate\Support\Str::limit(strip_tags((string) $summary), 110) }}</p>
        @endif
        <div class="mt-auto pt-4">
            <div class="mb-4 border-t border-black/5 pt-4">
                <div class="flex min-h-11 items-center gap-2">
                    @if ($merchant->phone)
                        <a href="tel:{{ $merchant->phone }}" class="icon-btn" aria-label="{{ __('Appeler') }}" title="{{ __('Appeler') }}" @click.stop>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        </a>
                    @endif
                    <a href="{{ $merchant->directionsUrl() }}" class="icon-btn" target="_blank" rel="noreferrer" aria-label="{{ __('Itinéraire') }}" title="{{ __('Itinéraire') }}" @click.stop>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                    </a>
                    @if ($merchant->email)
                        <a href="mailto:{{ $merchant->email }}" class="icon-btn" aria-label="{{ __('Email') }}" title="{{ __('Email') }}" @click.stop>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </a>
                    @endif
                    @if ($merchant->website)
                        <a href="{{ $merchant->website }}" class="icon-btn" target="_blank" rel="noreferrer" aria-label="{{ __('Site web') }}" title="{{ __('Site web') }}" @click.stop>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/></svg>
                        </a>
                    @endif
                </div>
            </div>
            <a href="{{ aca_url('merchant', ['slug' => $merchant->t('slug')]) }}" class="btn-plum w-full text-sm py-2.5" @click.stop>{{ __('Voir la fiche') }} →</a>
        </div>
    </div>
</article>
