@extends('layouts.operator')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">Shop analytics</h1>
        <a href="{{ route('operator.analytics.export') }}" class="btn btn-sm btn-outline-dark">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card p-3 text-center"><div class="h5 mb-0">₱{{ number_format($summary['total_income'], 2) }}</div><div class="small text-muted">Total income</div></div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center"><div class="h5 mb-0">{{ number_format($summary['total_volume_kg'], 1) }}kg</div><div class="small text-muted">Total volume diverted</div></div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center"><div class="h5 mb-0">{{ $summary['households_served'] }}</div><div class="small text-muted">Households served</div></div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center"><div class="h5 mb-0">{{ number_format($summary['tsd_routed_kg'], 1) }}kg</div><div class="small text-muted">Routed to TSD</div></div>
        </div>
    </div>

    <h2 class="h6">By material</h2>
    <ul class="list-group mb-3">
        @forelse($byMaterial as $row)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ str_replace('_',' ', $row->material_type) }}</span>
                <span>{{ number_format($row->kg, 1) }}kg · ₱{{ number_format($row->total, 2) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted small">No transactions yet.</li>
        @endforelse
    </ul>

    <h2 class="h6">Last 8 weeks</h2>
    <ul class="list-group mb-3">
        @forelse($weekly as $w)
            <li class="list-group-item d-flex justify-content-between">
                <span class="mono small">Week {{ $w->yw }}</span>
                <span>{{ number_format($w->kg, 1) }}kg · ₱{{ number_format($w->total, 2) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted small">No history yet.</li>
        @endforelse
    </ul>

    <p class="small text-muted">
        Exportable as income and diverted-volume documentation for social-protection program applications (DAO 2024-1655).
    </p>
@endsection
