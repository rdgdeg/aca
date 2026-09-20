<a {{ $attributes->merge(['class' => 'listing-card']) }} href="{{ aca_url('event', ['slug' => $event->t('slug')]) }}">
    <div class="relative aspect-[16/10] overflow-hidden">
        <img src="{{ $event->cover() }}" alt="{{ $event->t('title') }}" class="h-full w-full object-cover" loading="lazy">
        <p class="cat-chip">{{ optional($event->starts_at)->translatedFormat('d M Y') }}</p>
    </div>
    <div class="flex flex-1 flex-col p-5">
        @if ($event->type)
            <p class="text-xs font-bold uppercase tracking-[0.16em] text-plum">{{ $event->type->t('name') }}</p>
        @endif
        <h3 class="mt-1 font-serif text-2xl">{{ $event->t('title') }}</h3>
        <p class="mt-2 mb-4 text-sm text-ink/70">{{ $event->location }}</p>
        <div class="mt-auto">
            <span class="btn-plum w-full text-sm py-2.5 px-4">{{ __('En savoir plus') }} →</span>
        </div>
    </div>
</a>
