@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 pt-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Parcours shopping') }}</p>
    <h1 class="mt-2 text-2xl font-semibold md:text-3xl">{{ __('Trois boucles, un centre-ville') }}</h1>
    <p class="mt-4 max-w-2xl text-ink/70">{{ __('Pas un catalogue : trois promenades à suivre selon l’envie. Chaque étape ouvre la fiche et l’itinéraire.') }}</p>
</section>

@foreach ($trails as $i => $trail)
    <section class="{{ $i % 2 === 1 ? 'bg-white' : '' }}">
        <div class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-gold">0{{ $i + 1 }}</p>
            <h2 class="mt-2 text-xl font-semibold md:text-2xl">{{ $trail['title'] }}</h2>
            <p class="mt-3 max-w-xl text-ink/70">{{ $trail['lead'] }}</p>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($trail['merchants'] as $step => $merchant)
                    <article class="relative">
                        <span class="absolute -left-2 -top-2 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-gold text-sm font-extrabold text-plum">{{ $step + 1 }}</span>
                        <x-merchant-card :merchant="$merchant" />
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endforeach
@endsection
