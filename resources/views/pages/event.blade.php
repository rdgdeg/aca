@extends('layouts.public')

@section('content')
<section class="relative h-[50vh] min-h-[320px]">
    <img src="{{ $event->cover() }}" alt="" class="absolute inset-0 h-full w-full object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-cream to-black/30"></div>
    <div class="absolute bottom-0 mx-auto w-full max-w-7xl px-4 pb-8 lg:px-8">
        <p class="text-sm text-plum">{{ optional($event->starts_at)->translatedFormat('l d F Y · H:i') }}</p>
        <h1 class="font-serif text-4xl">{{ $event->t('title') }}</h1>
    </div>
</section>
<section class="mx-auto max-w-3xl px-4 py-12">
    <p class="text-ink/70">{{ $event->location }} @if($event->price) · {{ $event->price }} @endif</p>
    <div class="prose mt-6 max-w-none">{{ $event->t('description') }}</div>
    <div class="mt-8 flex flex-wrap gap-3">
        <a class="btn-plum" href="{{ url(aca_path('event', ['slug' => $event->t('slug')]).'.ics') }}">{{ __('Ajouter à mon calendrier') }}</a>
        @if ($event->external_url)
            <a class="pill" href="{{ $event->external_url }}" target="_blank" rel="noreferrer">{{ __('Site de l’événement') }}</a>
        @endif
        @if ($event->merchant)
            <a class="pill" href="{{ aca_url('merchant', ['slug' => $event->merchant->t('slug')]) }}">{{ $event->merchant->name }}</a>
        @endif
    </div>
    @if ($event->form && $event->registrationOpen())
        <div class="mt-12 rounded-2xl bg-white p-6">
            <h2 class="font-serif text-2xl mb-2">{{ __('Inscription') }}</h2>
            @if ($event->capacity)
                <p class="text-sm mb-4">{{ __(':n places restantes', ['n' => $event->remainingPlaces()]) }}</p>
            @endif
            <livewire:public-form :form="$event->form" :event-id="$event->id" />
        </div>
    @endif
</section>
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Event',
    'name' => $event->t('title'),
    'startDate' => optional($event->starts_at)?->toIso8601String(),
    'endDate' => optional($event->ends_at)?->toIso8601String(),
    'location' => ['@type' => 'Place', 'name' => $event->location, 'address' => $event->location],
    'image' => $event->cover(),
    'description' => $event->t('description'),
], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
@endsection
