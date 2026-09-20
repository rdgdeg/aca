<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-2">
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h3 class="font-semibold mb-3">Avant</h3>
            @foreach ($this->record->original ?? [] as $field => $value)
                <p class="text-sm mb-2"><strong>{{ $field }}</strong><br>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</p>
            @endforeach
        </div>
        <div class="rounded-xl bg-white p-4 shadow-sm">
            <h3 class="font-semibold mb-3">Après</h3>
            @foreach ($this->record->payload ?? [] as $field => $value)
                <p class="text-sm mb-2"><strong>{{ $field }}</strong><br>{{ is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value }}</p>
            @endforeach
        </div>
    </div>
    <p class="mt-4 text-sm text-gray-500">{{ $this->record->sender_name }} · {{ $this->record->sender_email }} · {{ $this->record->sender_role }} · {{ $this->record->merchant?->name }}</p>
</x-filament-panels::page>
