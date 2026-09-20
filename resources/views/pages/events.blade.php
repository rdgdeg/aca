@extends('layouts.public')

@section('content')
@php
    $agendaQuery = array_filter([
        'archives' => request('archives') ?: null,
        'type' => request('type') ?: null,
    ]);
    $agendaUrl = function (?string $monthKey = null) use ($agendaQuery): string {
        $query = $agendaQuery;
        if ($monthKey) {
            $query['month'] = $monthKey;
        }

        $base = aca_url('events');

        return $query === [] ? $base : $base.'?'.http_build_query($query);
    };
@endphp
<section class="mx-auto max-w-7xl px-4 pt-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Agenda') }}</p>
    <h1 class="font-serif text-4xl mt-2">{{ __('La ville en mouvement') }}</h1>
    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ aca_url('events') }}" class="pill {{ !request('archives') && !request('month') && !request('type') ? 'is-active' : '' }}">{{ __('À venir') }}</a>
        <a href="{{ aca_url('events') }}?archives=1" class="pill {{ request('archives') ? 'is-active' : '' }}">{{ __('Archives') }}</a>
        @foreach ($types as $type)
            <a href="{{ aca_url('events') }}?type={{ $type->slug }}{{ request('archives') ? '&archives=1' : '' }}" class="pill {{ request('type') === $type->slug ? 'is-active' : '' }}">{{ $type->t('name') }}</a>
        @endforeach
        <a href="{{ url('/calendar.ics') }}" class="pill">iCal</a>
        <a href="{{ aca_url('propose-event') }}" class="pill">{{ __('Proposer un événement') }}</a>
    </div>
    @if ($monthCounts->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ $agendaUrl() }}" class="pill {{ !$filterMonth ? 'is-active' : '' }}">{{ __('Tous les mois') }}</a>
            @foreach ($monthCounts as $item)
                <a href="{{ $agendaUrl($item['key']) }}" class="pill {{ $filterMonth && $month->format('Y-m') === $item['key'] ? 'is-active' : '' }}">
                    {{ $item['label'] }} ({{ $item['count'] }})
                </a>
            @endforeach
        </div>
    @endif
</section>
<section class="mx-auto max-w-7xl px-4 py-10 lg:px-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="font-serif text-3xl capitalize">{{ $month->translatedFormat('F Y') }}</h2>
        <div class="flex gap-2">
            <a class="pill" href="{{ $agendaUrl($month->copy()->subMonth()->format('Y-m')) }}">←</a>
            <a class="pill" href="{{ $agendaUrl($month->copy()->addMonth()->format('Y-m')) }}">→</a>
        </div>
    </div>
    <div class="grid grid-cols-7 gap-px overflow-hidden rounded-2xl bg-plum/10 text-sm">
        @foreach ([__('Lun'), __('Mar'), __('Mer'), __('Jeu'), __('Ven'), __('Sam'), __('Dim')] as $label)
            <div class="bg-plum px-2 py-2 text-center text-xs uppercase tracking-wider text-white">{{ $label }}</div>
        @endforeach
        @for ($i = 1; $i < $month->isoWeekday(); $i++)
            <div class="min-h-24 bg-mist/60"></div>
        @endfor
        @for ($day = 1; $day <= $month->daysInMonth; $day++)
            @php $date = $month->copy()->day($day); $key = $date->toDateString(); @endphp
            <div class="min-h-24 bg-white p-2 {{ $date->isToday() ? 'ring-2 ring-inset ring-plum' : '' }}">
                <p class="text-xs text-ink/50">{{ $day }}</p>
                @foreach ($calendarEvents->get($key, collect()) as $event)
                    <a href="{{ aca_url('event', ['slug' => $event->t('slug')]) }}" class="mt-1 block truncate text-plum">{{ $event->t('title') }}</a>
                @endforeach
            </div>
        @endfor
    </div>
</section>
<section class="mx-auto max-w-7xl px-4 pb-16 lg:px-8">
    <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        @forelse ($events as $event)
            <x-event-card :event="$event" />
        @empty
            <p>{{ __('Aucun événement pour cette période.') }}</p>
        @endforelse
    </div>
    @if ($events->hasPages())
        <div class="mt-8">
            {{ $events->onEachSide(1)->links('pagination.aca') }}
        </div>
    @endif
</section>
@endsection
