@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 pt-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-gold">{{ __('Week-end à Ath') }}</p>
    <h1 class="font-serif text-5xl mt-2">{{ $friday->translatedFormat('d') }} – {{ $sunday->translatedFormat('d F Y') }}</h1>
    <p class="mt-4 max-w-2xl text-ink/70">{{ __('Les rendez-vous du week-end et les vitrines ouvertes : un plan simple pour samedi et dimanche.') }}</p>
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ aca_url('events') }}" class="pill">{{ __('Agenda') }}</a>
        <a href="{{ aca_url('parking') }}" class="pill">{{ __('Parking & accès') }}</a>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 lg:px-8">
    <h2 class="font-serif text-3xl">{{ __('Sur l’agenda') }}</h2>
    <div class="mt-6 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <x-event-card :event="$event" />
        @empty
            <p class="col-span-full text-ink/70">{{ __('Rien de marqué pour ce week-end : les vitrines, elles, restent ouvertes.') }}</p>
        @endforelse
    </div>
</section>

<section class="bg-white">
    <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
        <h2 class="font-serif text-3xl">{{ __('Vitrines ouvertes') }}</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($openShops as $merchant)
                <x-merchant-card :merchant="$merchant" />
            @endforeach
        </div>
    </div>
</section>
@endsection
