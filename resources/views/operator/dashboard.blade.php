@extends('layouts.operator')

@section('content')
    <p class="text-muted mb-1">Magandang araw, <b>{{ explode(' ', auth()->user()->name)[0] }}!</b></p>

    <div class="card p-3 mb-3 text-white" style="background:var(--junk);border:none;">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <div class="small" style="color:#F3E3C6;">Bought today</div>
                <div class="display-6" style="font-family:serif;">{{ number_format($boughtTodayKg, 1) }}kg</div>
            </div>
            <span class="badge" style="background:#7A4E11;color:#F3D8A4;">₱{{ number_format($earnedTodayPhp, 2) }} today</span>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card p-3 text-center">
                <div class="h4 mb-0">{{ $pendingCount }}</div>
                <div class="small text-muted">Pending requests</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center">
                <div class="h4 mb-0">{{ number_format($boughtTodayKg, 1) }}kg</div>
                <div class="small text-muted">Bought today</div>
            </div>
        </div>
    </div>

    <h2 class="h6">Quick actions</h2>
    <div class="row g-2 mb-3">
        <div class="col-6">
            <a href="{{ route('operator.requests.index') }}" class="card p-3 text-decoration-none text-dark badge-junk">
                <i class="bi bi-inbox fs-4"></i>
                <div class="fw-semibold mt-1">View requests</div>
                <div class="small">{{ $pendingCount }} pickups waiting</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('operator.prices.edit') }}" class="card p-3 text-decoration-none text-dark badge-tapon">
                <i class="bi bi-currency-exchange fs-4"></i>
                <div class="fw-semibold mt-1">Update prices</div>
                <div class="small">Keep buyers accurate</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('operator.transactions.index') }}" class="card p-3 text-decoration-none text-dark badge-siklo">
                <i class="bi bi-journal-text fs-4"></i>
                <div class="fw-semibold mt-1">Log transaction</div>
                <div class="small">Record a buy</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('operator.profile.edit') }}" class="card p-3 text-decoration-none text-dark badge-bantay">
                <i class="bi bi-shop fs-4"></i>
                <div class="fw-semibold mt-1">Shop profile</div>
                <div class="small">{{ $junkshop->is_accredited_tsd ? 'TSD accredited' : 'TSD pending' }}</div>
            </a>
        </div>
    </div>

    <h2 class="h6">Today's activity</h2>
    <ul class="list-group">
        @forelse($todaysActivity as $t)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ ucfirst(str_replace('_',' ', $t->material_type)) }} — {{ $t->resident->name ?? 'Walk-in' }}</span>
                <span class="badge-junk px-2 py-1 rounded">₱{{ number_format($t->price_total, 2) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted small">No transactions logged yet today.</li>
        @endforelse
    </ul>
@endsection
