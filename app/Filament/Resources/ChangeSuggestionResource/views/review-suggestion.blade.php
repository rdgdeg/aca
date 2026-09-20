<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h3 class="font-semibold mb-3">Avant</h3>
            @foreach ($record->original ?? [] as $field => $value)
                <p class="text-sm mb-2"><strong>{{ $field }}</strong><br>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</p>
            @endforeach
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h3 class="font-semibold mb-3">Après</h3>
            @foreach ($record->payload ?? [] as $field => $value)
                <p class="text-sm mb-2 text-primary-600"><strong>{{ $field }}</strong><br>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</p>
            @endforeach
        </div>
    </div>
    <p class="mt-4 text-sm text-gray-500">{{ $record->sender_name }} · {{ $record->sender_email }} · {{ $record->sender_role }}</p>
</x-filament-panels::page>
