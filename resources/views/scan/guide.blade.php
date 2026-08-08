@extends('layouts.resident')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@push('styles')
<style>
    .dropoff-map {
        width: 100%;
        height: 320px;
        min-height: 320px;
        border-radius: 18px;
        overflow: hidden;
        background: #e7ebdc;
    }

    .dropoff-map .leaflet-control-zoom {
        display: none;
    }

    .dropoff-map .leaflet-control-attribution {
        font-size: 8px;
        opacity: 0.7;
    }

    .junkshop-card {
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }

    .junkshop-card.selected {
        border-color: var(--tapon) !important;
        background: #f3fff8;
        box-shadow: 0 0 0 1px rgba(0, 128, 96, 0.12);
    }

    .junkshop-card:hover {
        border-color: #93c5aa;
    }

    .guide-note {
        background: #f7f8f2;
        border: 1px solid #e3e7d8;
        border-radius: 16px;
        padding: 14px 16px;
    }
</style>
@endpush

@section('content')
@php
    $tips = [
        'biodegradable' => "Compost at home or place in your barangay's green bin.",
        'recyclable' => 'Rinse and bring to a junkshop or MRF for the best value.',
        'residual' => "Bag securely and place out on your barangay's residual collection day.",
        'special_hazardous' => 'Do not mix with regular trash. Bring to an accredited TSD facility.',
        'e_waste' => 'Never give to informal collectors. Route to an accredited TSD facility (DAO 2024-1655).',
    ];

   $dropoffs = $mapJunkshops->map(fn ($s) => [
        'id' => $s->id,
        'name' => $s->name,
        'address' => $s->address,
        'lat' => (float) $s->latitude,
        'lng' => (float) $s->longitude,
    ])->values();
@endphp

<div class="mb-3">
    <div class="fw-semibold fs-5">Disposal Guide</div>
    <div class="text-muted small">
        Select a junkshop first. The disposal will be requested, then the junkshop must approve it before the transaction is considered complete.
        No points are awarded in this resident flow.
    </div>
</div>

<div class="card p-3 mb-3">
    <div class="fw-semibold text-capitalize mb-1">
        {{ str_replace('_', ' ', $wasteScan->ai_classification) }}
    </div>

    <p class="small mb-2">
        {{ $tips[$wasteScan->ai_classification] ?? 'No disposal tip available.' }}
    </p>

    @if($barangay)
        <div class="small text-muted">
            Next collection day in {{ $barangay->name }}:
            {{ $barangay->collection_schedule[$wasteScan->ai_classification === 'biodegradable' ? 'biodegradable' : ($wasteScan->ai_classification === 'residual' ? 'residual' : 'recyclable')] ?? 'Check with your barangay' }}
        </div>
    @endif
</div>

@if($nearbyJunkshops->isNotEmpty())
    <h2 class="h6">Nearby drop-off points</h2>

    <div id="dropoff-map" class="dropoff-map mb-3"></div>

    <form method="POST" action="{{ route('scan.confirm-disposal', $wasteScan) }}">
        @csrf

        <input type="hidden" name="junkshop_id" id="junkshop_id" value="">

        <ul class="list-group mb-3">
            @foreach($nearbyJunkshops as $shop)
                <li
                    class="list-group-item d-flex justify-content-between align-items-center junkshop-card"
                    data-junkshop-id="{{ $shop->id }}"
                >
                    <div>
                        <div class="fw-semibold">{{ $shop->name }}</div>
                        <div class="small text-muted">{{ $shop->address }}</div>
                        <a href="{{ route('market.show', $shop) }}" class="small">View details &rarr;</a>
                    </div>

                    <div class="d-flex flex-column align-items-end gap-2">
                        <a
                            class="btn btn-sm btn-outline-secondary"
                            href="https://www.google.com/maps/dir/?api=1&destination={{ $shop->latitude }},{{ $shop->longitude }}"
                            target="_blank"
                            rel="noopener"
                        >
                            <i class="bi bi-signpost-2"></i> Route
                        </a>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-success select-junkshop-btn"
                            data-junkshop-id="{{ $shop->id }}"
                        >
                            Select
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        @error('junkshop_id')
            <div class="text-danger small mb-2">{{ $message }}</div>
        @enderror

        <button
            type="submit"
            id="confirmDisposalBtn"
            class="btn w-100"
            style="background:var(--tapon);color:#fff;"
            disabled
        >
            Request disposal approval
        </button>
    </form>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const dropoffs = @json($dropoffs);
                const junkshopInput = document.getElementById('junkshop_id');
                const confirmBtn = document.getElementById('confirmDisposalBtn');
                const cards = document.querySelectorAll('.junkshop-card');
                const selectButtons = document.querySelectorAll('.select-junkshop-btn');

                let map = null;
                let userMarker = null;

                function setSelectedJunkshop(id) {
                    junkshopInput.value = id;
                    confirmBtn.disabled = !id;

                    cards.forEach(card => {
                        card.classList.toggle('selected', card.dataset.junkshopId === String(id));
                    });
                }

                function bindSelection() {
                    cards.forEach(card => {
                        card.addEventListener('click', function (e) {
                            if (e.target.closest('a') || e.target.closest('button')) {
                                return;
                            }

                            setSelectedJunkshop(this.dataset.junkshopId);
                        });
                    });

                    selectButtons.forEach(button => {
                        button.addEventListener('click', function () {
                            setSelectedJunkshop(this.dataset.junkshopId);
                        });
                    });
                }

                function initMap(centerLat, centerLng) {
                    map = L.map('dropoff-map', {
                        zoomControl: false
                    }).setView([centerLat, centerLng], 13);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors',
                    }).addTo(map);

                    if (dropoffs.length > 0) {
                        const bounds = [];

                        dropoffs.forEach((d) => {
                            const marker = L.marker([d.lat, d.lng]).addTo(map);

                            marker.bindPopup(
                                `<strong>${d.name}</strong><br>${d.address}<br><button type="button" class="btn btn-sm btn-success mt-2 js-select-map" data-junkshop-id="${d.id}">Select this junkshop</button>`
                            );

                            marker.on('click', function () {
                                setSelectedJunkshop(d.id);
                            });

                            bounds.push([d.lat, d.lng]);
                        });

                        if (bounds.length > 0) {
                            map.fitBounds(bounds, { padding: [25, 25] });
                        }
                    }

                    setTimeout(() => {
                        map.invalidateSize();
                    }, 250);
                }

                bindSelection();

                if (dropoffs.length > 0) {
                    const avgLat = dropoffs.reduce((s, d) => s + d.lat, 0) / dropoffs.length;
                    const avgLng = dropoffs.reduce((s, d) => s + d.lng, 0) / dropoffs.length;
                    initMap(avgLat, avgLng);
                } else {
                    const mapEl = document.getElementById('dropoff-map');
                    if (mapEl) {
                        mapEl.innerHTML = '<div class="p-3 small text-muted">No junkshops found nearby yet.</div>';
                    }
                    confirmBtn.disabled = true;
                }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        function (pos) {
                            const userLat = pos.coords.latitude;
                            const userLng = pos.coords.longitude;

                            if (map) {
                                if (userMarker) {
                                    map.removeLayer(userMarker);
                                }

                                userMarker = L.marker([userLat, userLng]).addTo(map)
                                    .bindPopup('You are here');

                                map.setView([userLat, userLng], 13);
                                setTimeout(() => map.invalidateSize(), 250);
                            }
                        },
                        function () {
                        },
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                }
            });
        </script>
    @endpush
@else
    <div class="alert alert-warning">
        No nearby junkshops were loaded yet. Please allow location access so we can show the closest drop-off points.
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!navigator.geolocation) return;

                navigator.geolocation.getCurrentPosition(
                    function (pos) {
                        const url = new URL(window.location.href);
                        url.searchParams.set('lat', pos.coords.latitude);
                        url.searchParams.set('lng', pos.coords.longitude);
                        window.location.replace(url.toString());
                    },
                    function () {
                        
                    },
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            });
        </script>
    @endpush
@endif
@endsection