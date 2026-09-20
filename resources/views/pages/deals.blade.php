@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 py-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-gold">{{ __('Bons plans') }}</p>
    <h1 class="mt-2 text-2xl font-semibold md:text-3xl">{{ __('Envies du moment') }}</h1>
    <div class="mt-10 grid gap-6 md:grid-cols-3">
        @forelse ($deals as $deal)
            <article class="photo-card">
                <img src="{{ $deal->cover() }}" alt="" class="aspect-[16/10] w-full object-cover">
                <div class="p-5">
                    <p class="text-xs text-gold">{{ __('Plus que :n jours', ['n' => $deal->daysLeft()]) }}</p>
                    <h2 class="mt-1 text-lg font-semibold">{{ $deal->t('title') }}</h2>
                    <p class="text-sm mt-2">{{ $deal->t('description') }}</p>
                    <p class="text-sm text-ink/60 mt-3">{{ $deal->t('conditions') }}</p>
                    @if ($deal->merchant)
                        <a class="text-plum text-sm mt-3 inline-block" href="{{ aca_url('merchant', ['slug' => $deal->merchant->t('slug')]) }}">{{ $deal->merchant->name }}</a>
                    @endif
                </div>
            </article>
        @empty
            <p>{{ __('Aucun bon plan en cours.') }}</p>
        @endforelse
    </div>
    <p class="mt-10"><a class="pill" href="{{ aca_url('propose-deal') }}">{{ __('Proposer un bon plan') }}</a></p>
</section>
@endsection
