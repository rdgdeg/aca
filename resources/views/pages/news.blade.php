@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-6xl px-4 py-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Actualités') }}</p>
    <h1 class="mt-2 text-2xl font-semibold md:text-3xl">{{ __('La vie de l’association') }}</h1>
    <div class="mt-10 grid gap-4 md:grid-cols-6">
        @foreach ($posts as $post)
            @php
                $wide = $loop->index % 5 === 0;
            @endphp
            <article class="listing-card {{ $wide ? 'md:col-span-4' : 'md:col-span-2' }}">
                <img src="{{ $post->cover() }}" alt="" class="w-full object-cover {{ $wide ? 'aspect-[16/8] md:aspect-[16/7]' : 'aspect-[16/10]' }}">
                <div class="flex flex-1 flex-col p-5 {{ $wide ? 'md:p-7' : '' }}">
                    <p class="text-xs text-ink/50">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
                    <h2 class="mt-1 font-semibold {{ $wide ? 'text-xl md:text-2xl' : 'text-lg' }}">{{ $post->t('title') }}</h2>
                    <p class="text-sm text-ink/70 mt-2 {{ $wide ? 'line-clamp-4' : 'line-clamp-3' }}">{{ $post->t('excerpt') }}</p>
                    <a href="{{ aca_url('post', ['slug' => $post->t('slug')]) }}" class="mt-auto pt-4 text-sm font-medium text-plum/55 hover:text-plum">{{ __('Lire la fiche') }} →</a>
                </div>
            </article>
        @endforeach
    </div>
    <div class="mt-8">{{ $posts->onEachSide(1)->links('pagination.aca') }}</div>
</section>
@endsection
