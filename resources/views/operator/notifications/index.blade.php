@extends('layouts.operator')

@section('content')
    <h1 class="h5 mb-3">Notifications</h1>

    <ul class="list-group">
        @forelse($notifications as $n)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <span class="badge-{{ $n->type === 'epr_match' ? 'siklo' : 'junk' }} px-2 py-1 rounded small">
                        {{ str_replace('_', ' ', $n->type) }}
                    </span>
                    <span class="small text-muted">{{ $n->created_at->diffForHumans() }}</span>
                </div>
                <p class="mb-0 mt-1">{{ $n->message }}</p>
            </li>
        @empty
            <li class="list-group-item text-muted small">No notifications yet.</li>
        @endforelse
    </ul>
@endsection
