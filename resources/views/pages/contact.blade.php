@extends('layouts.public')

@section('content')
<section class="band-white">
    <div class="mx-auto max-w-6xl px-4 pt-12 pb-6 lg:px-8">
        <p class="text-xs uppercase tracking-[0.25em] text-plum">ACA</p>
        <h1 class="mt-2 text-2xl font-semibold md:text-3xl">{{ __('Contact') }}</h1>
        <p class="mt-4 max-w-2xl text-ink/70">{{ __('Une question pour le comité, un partenariat, un passage presse : écrivez-nous.') }}</p>
    </div>
</section>

<section id="ecrire" class="band-alt">
    <div class="mx-auto max-w-6xl px-4 py-14 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-[minmax(0,20rem)_1fr]">
            <div class="grid gap-5 lg:grid-rows-[auto_1fr]">
                <div>
                    <span class="mark-bar"></span>
                    <p class="section-kicker">{{ __('Accueil') }}</p>
                    <h2 class="mt-2 text-xl font-semibold md:text-2xl">{{ __('Écrire au comité') }}</h2>
                    <p class="mt-3 text-ink/70">{{ __('Le secrétariat relit chaque message et revient vers vous.') }}</p>
                </div>
                <div class="grid gap-4 lg:grid-rows-3">
                    <div class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-plum">{{ __('Siège') }}</p>
                        <p class="mt-2 text-sm">Rue Ernest Cambier 2/1<br>7800 Ath</p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-plum">{{ __('Téléphone') }}</p>
                        <p class="mt-2 text-sm">
                            <a class="text-plum" href="tel:+32485926080">+32 485 92 60 80</a><br>
                            <a class="text-plum" href="mailto:president@athinfo.be">president@athinfo.be</a>
                        </p>
                    </div>
                    <div class="rounded-3xl bg-white p-5 ring-1 ring-black/5">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-plum">Facebook</p>
                        <p class="mt-2 text-sm text-ink/70">{{ __('Association des commerçants et artisans d’Ath') }}</p>
                        <a href="https://www.facebook.com/aca.commercantsdath/" rel="noreferrer" target="_blank" class="mt-3 inline-block text-sm font-medium text-plum hover:underline">{{ __('En savoir plus') }} →</a>
                    </div>
                </div>
            </div>
            <div class="rounded-[2rem] bg-white p-6 shadow-xl ring-1 ring-plum/15 md:p-10">
                @if ($form)
                    <livewire:public-form :form="$form" />
                @endif
            </div>
        </div>
    </div>
</section>

<section id="association" class="band-white">
    <div class="mx-auto max-w-6xl px-4 section-space lg:px-8">
        <div class="grid gap-10 lg:grid-cols-2 lg:items-center">
            <div>
                <span class="mark-bar"></span>
                <p class="section-kicker">{{ __('Depuis 1911') }}</p>
                <h2 class="mt-2 text-xl font-semibold md:text-2xl">{{ __('Le commerce athois, défendu ensemble.') }}</h2>
                <p class="mt-5 max-w-xl text-ink/75">{{ __('Fondée en 1911, l’Association des Commerçants et Artisans d’Ath défend le commerce de proximité, anime le centre-ville et accueille chaque année la Ducasse.') }}</p>
                <a href="{{ aca_url('join') }}" class="btn-plum mt-7">{{ __('Devenir membre') }}</a>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <img src="{{ asset('images/hero/ath.jpg') }}" alt="" class="col-span-2 h-52 w-full rounded-2xl object-cover">
                <a href="{{ aca_url('news') }}" class="rounded-2xl bg-[#F6F1E8] p-5">
                    <p class="text-lg font-semibold">{{ __('La vie de l’association') }}</p>
                    <p class="mt-4 text-sm font-medium text-plum/70">{{ __('En savoir plus') }} →</p>
                </a>
                <a href="{{ aca_url('events') }}" class="rounded-2xl bg-[#F6F1E8] p-5">
                    <p class="text-lg font-semibold">{{ __('Agenda') }}</p>
                    <p class="mt-4 text-sm font-medium text-plum/70">{{ __('En savoir plus') }} →</p>
                </a>
            </div>
        </div>
    </div>
</section>

<section class="band-alt">
    <div class="mx-auto max-w-6xl px-4 section-space lg:px-8">
        <span class="mark-bar"></span>
        <h2 class="text-xl font-semibold md:text-2xl">{{ __('Le comité') }}</h2>
        <p class="mt-2 max-w-xl text-ink/70">{{ __('Les commerçants n’ont pas de compte : une suggestion de modification suffit pour tenir une fiche à jour.') }}</p>
        <div class="mt-8 grid gap-4 md:grid-cols-2 md:max-w-3xl">
            @foreach ($committee as $member)
                <article class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-black/5">
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-plum">{{ $member['role'] }}</p>
                    <h3 class="mt-2 text-lg font-semibold">{{ $member['name'] }}</h3>
                    <p class="mt-3 text-sm">
                        <a class="text-plum" href="mailto:{{ $member['email'] }}">{{ $member['email'] }}</a>
                        @if ($member['phone'])
                            <br><a class="text-ink/70" href="tel:{{ preg_replace('/\s+/', '', $member['phone']) }}">{{ $member['phone'] }}</a>
                        @endif
                    </p>
                </article>
            @endforeach
        </div>
    </div>
</section>
@endsection
