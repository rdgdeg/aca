@extends('layouts.public')

@section('content')
<section class="mx-auto max-w-7xl px-4 pt-12 lg:px-8">
    <p class="text-xs uppercase tracking-[0.25em] text-plum">{{ __('Carte') }}</p>
    <h1 class="mt-2 text-2xl font-semibold md:text-3xl">{{ __('Ath sur le bout des doigts') }}</h1>
    <div class="mt-6 flex flex-wrap gap-2" id="map-filters">
        <button data-cat="" class="pill is-active">{{ __('Tout afficher') }}</button>
        @foreach ($categories as $category)
            <button data-cat="{{ $category->id }}" class="pill">{{ $category->t('name') }}</button>
        @endforeach
    </div>
</section>
<section class="mt-6 h-[70vh] min-h-[480px]">
    <div id="aca-map" class="h-full w-full"></div>
</section>
@endsection

@push('head')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
@endpush
@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
<script>
    const points = @json($points);
    const map = L.map('aca-map').setView([50.6305, 3.778], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap' }).addTo(map);
    let cluster = L.markerClusterGroup();
    function draw(cat) {
        cluster.clearLayers();
        points.filter(p => !cat || p.cat.includes(Number(cat))).forEach(p => {
            const icon = L.divIcon({ className: 'map-pin', html: `<span style="background:${p.color};width:14px;height:14px;border-radius:50%;display:block;border:2px solid white"></span>`, iconSize: [18, 18] });
            const marker = L.marker([p.lat, p.lng], { icon });
            marker.bindPopup(`<a href="${p.url}"><img src="${p.cover}" style="width:100%;height:90px;object-fit:cover;border-radius:8px"><strong>${p.name}</strong><br>${p.address || ''}</a>`);
            cluster.addLayer(marker);
        });
        map.addLayer(cluster);
    }
    draw('');
    document.querySelectorAll('#map-filters button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('#map-filters button').forEach(b => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            draw(btn.dataset.cat);
        });
    });
</script>
@endpush
