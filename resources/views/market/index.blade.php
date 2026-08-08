@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Find a Junkshop</h1>

    <form method="GET" class="mb-3">
        <select name="material" class="form-select" onchange="this.form.submit()">
            <option value="">All materials</option>
            @foreach($materials as $m)
                <option value="{{ $m }}" @selected($material === $m)>{{ str_replace('_',' ', $m) }}</option>
            @endforeach
        </select>
    </form>

    <ul class="list-group">
        @forelse($junkshops as $shop)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('market.show', $shop) }}" class="fw-semibold text-decoration-none">{{ $shop->name }}</a>
                    @if($shop->is_accredited_tsd)
                        <span class="badge badge-siklo">Accredited TSD</span>
                    @endif
                </div>
                <div class="small text-muted">{{ $shop->address }} · {{ $shop->operating_hours }}</div>
                <div class="small mt-1">
                    @foreach($shop->materialPrices as $p)
                        <span class="badge badge-junk me-1">{{ $p->material_type }} ₱{{ number_format($p->price_per_kg, 2) }}/kg</span>
                    @endforeach
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted small">No junkshops match that material.</li>
        @endforelse
    </ul>

    <a href="{{ route('market.history') }}" class="d-block text-center small mt-3">View my transaction history →</a>
@endsection
