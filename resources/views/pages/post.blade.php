@extends('layouts.public')

@section('content')
<article class="mx-auto max-w-3xl px-4 py-16">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ $post->is_press_release ? __('Presse') : __('Actualités') }}</p>
    <h1 class="font-serif text-4xl mt-3">{{ $post->t('title') }}</h1>
    <p class="mt-3 text-ink/50">{{ optional($post->published_at)->translatedFormat('d F Y') }}</p>
    <img src="{{ $post->cover() }}" alt="" class="mt-8 w-full rounded-2xl object-cover aspect-[16/9]">
    <div class="prose mt-8 max-w-none">{!! nl2br(e($post->t('body'))) !!}</div>
</article>
@endsection
