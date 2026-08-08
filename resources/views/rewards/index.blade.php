@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">SikloPoints</h1>

    <div class="card p-3 mb-3 text-center" style="background:var(--siklo-tint);border:none;">
        <div class="text-muted small">Available balance</div>
        <div class="display-6" style="font-family:serif;">{{ number_format($user->current_points) }}</div>
        <a href="{{ route('rewards.leaderboard') }}" class="small mt-2">View leaderboard →</a>
    </div>

    <h2 class="h6">Redeem</h2>
    <div class="row g-2 mb-3">
        @foreach($catalog as $key => $reward)
            <div class="col-12">
                <div class="card p-3 d-flex flex-row justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $reward['label'] }}</div>
                        <div class="small text-muted">{{ $reward['points'] }} pts</div>
                    </div>
                    <form method="POST" action="{{ route('rewards.redeem', $key) }}">
                        @csrf
                        <button class="btn btn-sm {{ $user->current_points >= $reward['points'] ? '' : 'btn-secondary disabled' }}"
                                style="{{ $user->current_points >= $reward['points'] ? 'background:var(--siklo);color:#fff;' : '' }}">
                            Redeem
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>

    <h2 class="h6">Recent activity</h2>
    <ul class="list-group mb-3">
        @foreach($ledger as $entry)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ str($entry->action_type)->headline() }}</span>
                <span class="{{ $entry->points_earned > 0 ? 'text-success' : 'text-danger' }}">
                    {{ $entry->points_earned > 0 ? '+'.$entry->points_earned : '-'.$entry->points_redeemed }}
                </span>
            </li>
        @endforeach
    </ul>

    @if($redemptions->isNotEmpty())
        <h2 class="h6">My redemptions</h2>
        <ul class="list-group">
            @foreach($redemptions as $r)
                <li class="list-group-item d-flex justify-content-between">
                    <span>{{ config('rewards.'.$r->redemption_type.'.label', $r->redemption_type) }}</span>
                    <span class="badge bg-secondary text-capitalize">{{ $r->status }}</span>
                </li>
            @endforeach
        </ul>
    @endif
@endsection
