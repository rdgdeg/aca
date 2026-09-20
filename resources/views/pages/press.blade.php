@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-4xl px-4 py-16">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Espace presse') }}</p>
    <h1 class="font-serif text-5xl mt-2">{{ __('Communiqués & kit') }}</h1>
    <p class="mt-4 text-ink/70">{{ __('Logos, photos et communiqués pour relayer la vie du centre-ville d’Ath.') }}</p>
    <ul class="mt-10 divide-y divide-black/5 bg-white rounded-2xl">
        @foreach ($releases as $post)
            <li class="p-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-serif text-xl">{{ $post->t('title') }}</p>
                    <p class="text-sm text-ink/50">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                </div>
                <a class="pill" href="{{ aca_url('post', ['slug' => $post->t('slug')]) }}">{{ __('Lire') }}</a>
            </li>
        @endforeach
    </ul>
    <p class="mt-8 text-sm">{{ __('Contact presse') }} : <a href="mailto:president@athinfo.be">president@athinfo.be</a></p>
</section>
@endsection
