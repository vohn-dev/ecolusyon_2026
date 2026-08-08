@extends('layouts.resident')

@section('content')
    <p class="text-muted mb-1">Magandang araw, <b>{{ explode(' ', $user->name)[0] }}!</b></p>

    <div class="card p-3 mb-3" style="background:var(--siklo-tint);border:none;">
        <div class="text-muted small">SikloPoints balance</div>
        <div class="display-6" style="font-family:serif;">{{ number_format($user->current_points) }}</div>
        <div class="text-muted small">{{ number_format($user->total_points) }} earned lifetime</div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-6">
            <a href="{{ route('scan.create') }}" class="card p-3 text-decoration-none text-dark badge-tapon">
                <i class="bi bi-camera fs-4"></i>
                <div class="fw-semibold mt-1">Scan Waste</div>
                <div class="small">TaponWise</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('reports.create') }}" class="card p-3 text-decoration-none text-dark badge-bantay">
                <i class="bi bi-exclamation-triangle fs-4"></i>
                <div class="fw-semibold mt-1">Report Hotspot</div>
                <div class="small">BantayBaha</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('market.index') }}" class="card p-3 text-decoration-none text-dark badge-junk">
                <i class="bi bi-recycle fs-4"></i>
                <div class="fw-semibold mt-1">Find Junkshop</div>
                <div class="small">JunkConnect</div>
            </a>
        </div>
        <div class="col-6">
            <a href="{{ route('rewards.index') }}" class="card p-3 text-decoration-none text-dark badge-siklo">
                <i class="bi bi-star fs-4"></i>
                <div class="fw-semibold mt-1">Rewards</div>
                <div class="small">SikloPoints</div>
            </a>
        </div>
    </div>

    <h2 class="h6">Recent activity</h2>
    <ul class="list-group">
        @forelse($activity as $entry)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>{{ str($entry->action_type)->headline() }}</span>
                <span class="badge bg-success">
                    +{{ $entry->points_earned }}
                </span>
            </li>
        @empty
            <li class="list-group-item text-muted small">
                No activity yet — scan an item or report a hotspot to get started.
            </li>
        @endforelse
    </ul>
@endsection
