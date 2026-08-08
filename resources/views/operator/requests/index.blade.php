@extends('layouts.operator')

@section('content')
    <h1 class="h5 mb-1">Pickup &amp; drop-off requests</h1>
    <p class="text-muted small mb-3">From households near your shop</p>

    @error('ewaste') <div class="alert alert-warning py-2 small">{{ $message }}</div> @enderror

    <div class="d-flex gap-2 mb-3">
        @foreach(['pending' => 'Pending', 'accepted' => 'Accepted', 'completed' => 'Completed'] as $key => $label)
            <a href="{{ route('operator.requests.index', ['status' => $key]) }}"
               class="btn btn-sm {{ $status === $key ? 'btn-dark' : 'btn-outline-secondary' }}">
                {{ $label }} ({{ $counts[$key] }})
            </a>
        @endforeach
    </div>

    @forelse($requests as $r)
        <div class="card p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <b>{{ $r->resident->name }}</b><br>
                    <span class="small text-muted">
                        {{ str_replace('_',' ', $r->material_type) }} · ~{{ $r->estimated_weight_kg }}kg
                    </span>
                </div>
                @if($r->is_ewaste)
                    <span class="badge-hazard px-2 py-1 rounded small">Route to TSD</span>
                @elseif($r->status === 'pending')
                    <span class="badge-junk px-2 py-1 rounded small">New</span>
                @endif
            </div>

            @if($r->is_ewaste)
                <p class="small text-muted mt-2 mb-2">
                    Flagged hazardous — accept only if your shop is TSD-accredited (see Shop Profile).
                </p>
            @endif

            @if($r->status === 'pending')
                <div class="d-flex gap-2 mt-2">
                    <form method="POST" action="{{ route('operator.requests.accept', $r) }}" class="flex-fill">
                        @csrf
                        <button class="btn btn-sm w-100" style="background:{{ $r->is_ewaste ? 'var(--bantay)' : 'var(--siklo)' }};color:#fff;">
                            {{ $r->is_ewaste ? 'Forward to TSD' : 'Accept' }}
                        </button>
                    </form>
                    <form method="POST" action="{{ route('operator.requests.decline', $r) }}" class="flex-fill">
                        @csrf
                        <button class="btn btn-sm btn-outline-danger w-100">Decline</button>
                    </form>
                </div>
            @elseif($r->status === 'accepted')
                <a href="{{ route('operator.transactions.index') }}" class="btn btn-sm mt-2 w-100" style="background:var(--junk);color:#fff;">
                    Log transaction
                </a>
            @else
                <span class="small text-muted">Completed {{ $r->updated_at->diffForHumans() }}</span>
            @endif
        </div>
    @empty
        <p class="text-muted small">No {{ $status }} requests.</p>
    @endforelse
@endsection
