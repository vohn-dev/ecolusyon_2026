@foreach($junkshops as $shop)
    <li class="list-group-item junkshop-item" data-lat="{{ $shop->latitude }}" data-lng="{{ $shop->longitude }}">
        <div class="d-flex justify-content-between">
            <a href="{{ route('market.show', $shop) }}" class="fw-semibold text-decoration-none">{{ $shop->name }}</a>
            @if($shop->is_accredited_tsd)
                <span class="badge badge-siklo">Accredited TSD</span>
            @endif
        </div>

        <div class="small text-muted">{{ $shop->address }}</div>

        <div class="d-flex align-items-center gap-2 small mt-1">
            <span><i class="bi bi-signpost-split"></i> <span class="js-distance">…</span></span>
            <span>
                <span class="badge {{ $shop->open_status['state'] === 'open' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $shop->open_status['state'] === 'open' ? 'Open' : 'Closed' }}
                </span>
                {{ $shop->open_status['label'] }}
            </span>
        </div>

        <div class="small mt-1 mb-2">
            @foreach($shop->materialPrices as $p)
                <span class="badge badge-junk me-1">{{ $p->material_type }} ₱{{ number_format($p->price_per_kg, 2) }}/kg</span>
            @endforeach
        </div>

        <a class="btn btn-sm btn-outline-secondary"
           href="https://www.google.com/maps/dir/?api=1&destination={{ $shop->latitude }},{{ $shop->longitude }}"
           target="_blank" rel="noopener">
            <i class="bi bi-signpost-2"></i> Route
        </a>
    </li>
@endforeach
