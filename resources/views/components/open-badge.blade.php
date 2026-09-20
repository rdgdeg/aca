@props(['status'])
@php
    $class = match ($status['key'] ?? '') {
        'open' => 'badge-open',
        'closing_soon' => 'badge-soon',
        default => 'badge-closed',
    };
@endphp
<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-3 py-1 text-xs font-semibold $class"]) }}>
    {{ $status['label'] }}
    @if (!empty($status['detail']))
        <span class="ml-1 font-normal opacity-80">· {{ $status['detail'] }}</span>
    @endif
</span>
