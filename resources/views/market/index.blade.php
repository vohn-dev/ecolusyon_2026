@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Find a Junkshop</h1>

    <div class="d-flex gap-2 mb-3 pb-1" style="overflow-x:auto;" id="material-filter">
        <a href="{{ route('market.index') }}"
           class="btn btn-sm {{ ! $material ? 'btn-junk-active' : 'btn-outline-secondary' }} flex-shrink-0">
            <i class="bi bi-grid"></i> All
        </a>
        @foreach($materialIcons as $m => $icon)
            <a href="{{ route('market.index', ['material' => $m]) }}"
               class="btn btn-sm {{ $material === $m ? 'btn-junk-active' : 'btn-outline-secondary' }} flex-shrink-0">
                <i class="bi {{ $icon }}"></i> {{ str_replace('_', ' ', $m) }}
            </a>
        @endforeach
    </div>

    <ul class="list-group mb-2" id="junkshop-list">
        @include('market._junkshop-items', ['junkshops' => $junkshops])
    </ul>

    <div id="lazy-sentinel" class="text-center small text-muted py-3" data-next-page="{{ $junkshops->hasMorePages() ? 2 : '' }}">
        @if(! $junkshops->hasMorePages() && $junkshops->isEmpty())
            No junkshops match that material.
        @endif
    </div>

    <a href="{{ route('market.history') }}" class="d-block text-center small mt-3">View my transaction history &rarr;</a>

    @push('styles')
        <style>.btn-junk-active{ background: var(--junk); color:#fff; border-color: var(--junk); }</style>
    @endpush

    @push('scripts')
    <script>
    const list = document.getElementById('junkshop-list');
    const sentinel = document.getElementById('lazy-sentinel');
    const currentMaterial = new URLSearchParams(window.location.search).get('material') ?? '';
    let loading = false;

    const observer = new IntersectionObserver(async (entries) => {
        const nextPage = sentinel.dataset.nextPage;
        if (! entries[0].isIntersecting || ! nextPage || loading) return;

        loading = true;
        sentinel.textContent = 'Loading more…';

        const url = new URL('{{ route('market.index') }}');
        if (currentMaterial) url.searchParams.set('material', currentMaterial);
        url.searchParams.set('page', nextPage);

        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();

        list.insertAdjacentHTML('beforeend', data.html);
        sentinel.dataset.nextPage = data.has_more ? (parseInt(nextPage) + 1) : '';
        sentinel.textContent = data.has_more ? '' : "You've reached the end of the list.";
        loading = false;
        attachDistances();
    }, { rootMargin: '200px' });

    observer.observe(sentinel);

    function haversineKm(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
        return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    }

    function attachDistances() {
        if (! navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition((pos) => {
            document.querySelectorAll('.junkshop-item').forEach((el) => {
                const span = el.querySelector('.js-distance');
                if (! span || span.dataset.done) return;
                const km = haversineKm(pos.coords.latitude, pos.coords.longitude, parseFloat(el.dataset.lat), parseFloat(el.dataset.lng));
                span.textContent = `${km.toFixed(1)} km away`;
                span.dataset.done = '1';
            });
        }, () => {
            document.querySelectorAll('.js-distance').forEach((span) => { span.textContent = 'Distance unavailable'; });
        });
    }

    attachDistances();
    </script>
    @endpush
@endsection
