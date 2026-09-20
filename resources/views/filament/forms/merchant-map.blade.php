@php
    $lat = filled($get('lat')) ? (float) $get('lat') : 50.6305;
    $lng = filled($get('lng')) ? (float) $get('lng') : 3.778;
    $hasPoint = filled($get('lat')) && filled($get('lng'));
@endphp
<div
    wire:ignore
    x-data="{
        lat: {{ $lat }},
        lng: {{ $lng }},
        init() {
            if (typeof L === 'undefined') {
                return;
            }
            this.map = L.map(this.$refs.map).setView([this.lat, this.lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OSM' }).addTo(this.map);
            this.marker = L.marker([this.lat, this.lng], { draggable: true }).addTo(this.map);
            const sync = (point) => {
                this.marker.setLatLng(point);
                $wire.set('data.lat', point.lat);
                $wire.set('data.lng', point.lng);
            };
            this.marker.on('dragend', (e) => sync(e.target.getLatLng()));
            this.map.on('click', (e) => sync(e.latlng));
            window.addEventListener('aca-gps-updated', (e) => {
                const point = e.detail || {};
                if (!point.lat || !point.lng) return;
                this.marker.setLatLng([point.lat, point.lng]);
                this.map.setView([point.lat, point.lng], 16);
            });
            setTimeout(() => this.map.invalidateSize(), 200);
        }
    }"
    class="space-y-2"
>
    <div x-ref="map" class="h-72 w-full overflow-hidden rounded-xl border border-gray-200"></div>
    <p class="text-xs text-gray-500">
        @if ($hasPoint)
            GPS : {{ number_format($lat, 6, '.', '') }}, {{ number_format($lng, 6, '.', '') }}
        @else
            Générez le GPS depuis l’adresse, ou cliquez sur la carte.
        @endif
    </p>
</div>
