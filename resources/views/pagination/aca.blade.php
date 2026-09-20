@php
    $isLivewire = isset($this) && $this instanceof \Livewire\Component;
@endphp

@if ($paginator->hasPages())
    <nav class="flex flex-col items-center gap-3" aria-label="{{ __('pagination.navigation') }}">
        <p class="text-sm text-ink/55">
            {{ __('pagination.range', [
                'first' => $paginator->firstItem(),
                'last' => $paginator->lastItem(),
                'total' => $paginator->total(),
            ]) }}
        </p>
        <div class="inline-flex items-center gap-1 rounded-full bg-white p-1 shadow-sm ring-1 ring-plum/15">
            @if ($paginator->onFirstPage())
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-ink/30" aria-disabled="true">‹</span>
            @elseif ($isLivewire)
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" wire:click.prevent="previousPage('{{ $paginator->getPageName() }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-plum hover:bg-mist" aria-label="{{ __('pagination.previous') }}">‹</a>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-plum hover:bg-mist" aria-label="{{ __('pagination.previous') }}">‹</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-1 text-ink/40">{{ $element }}</span>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-plum px-3 text-sm font-semibold text-white" aria-current="page">{{ $page }}</span>
                        @elseif ($isLivewire)
                            <a href="{{ $url }}" wire:click.prevent="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-sm font-medium text-plum hover:bg-mist" aria-label="{{ __('pagination.goto', ['page' => $page]) }}">{{ $page }}</a>
                        @else
                            <a href="{{ $url }}" class="inline-flex h-10 min-w-10 items-center justify-center rounded-full px-3 text-sm font-medium text-plum hover:bg-mist" aria-label="{{ __('pagination.goto', ['page' => $page]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                @if ($isLivewire)
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" wire:click.prevent="nextPage('{{ $paginator->getPageName() }}')" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-plum hover:bg-mist" aria-label="{{ __('pagination.next') }}">›</a>
                @else
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-plum hover:bg-mist" aria-label="{{ __('pagination.next') }}">›</a>
                @endif
            @else
                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full text-ink/30" aria-disabled="true">›</span>
            @endif
        </div>
    </nav>
@endif
