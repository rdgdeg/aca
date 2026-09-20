@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-16">
    @if ($page)
        <h1 class="font-serif text-4xl">{{ $page->t('title') }}</h1>
        <div class="mt-8 space-y-8">
            @foreach ($page->blocksForLocale() as $block)
                @if (($block['type'] ?? 'text') === 'text')
                    <div class="prose max-w-none">{!! nl2br(e($block['body'] ?? '')) !!}</div>
                @elseif (($block['type'] ?? '') === 'cta')
                    <a href="{{ $block['url'] ?? '#' }}" class="btn-plum">{{ $block['label'] ?? __('En savoir plus') }}</a>
                @elseif (($block['type'] ?? '') === 'form' && !empty($block['form_key']))
                    @php $form = \App\Models\Form::system($block['form_key']); @endphp
                    @if ($form)
                        <livewire:public-form :form="$form" :key="$form->id.'-'.$fallbackKey" />
                    @endif
                @endif
            @endforeach
        </div>
    @else
        <h1 class="font-serif text-4xl">{{ __('Page') }}</h1>
        <p class="mt-6">{{ __('Contenu à paraître.') }}</p>
    @endif
</section>
@endsection
