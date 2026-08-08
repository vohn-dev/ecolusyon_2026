@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">My Transactions</h1>
    <ul class="list-group">
        @forelse($transactions as $t)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <span class="text-capitalize">{{ str_replace('_',' ', $t->material_type) }} — {{ $t->junkshop->name }}</span>
                    <span>₱{{ number_format($t->price_total, 2) }}</span>
                </div>
                <div class="small text-muted">
                    {{ $t->weight_kg }} kg · {{ $t->created_at->diffForHumans() }} · +{{ $t->points_awarded }} pts
                    @if($t->routed_to_tsd) · <span class="badge badge-siklo">TSD routed</span> @endif
                </div>
            </li>
        @empty
            <li class="list-group-item text-muted small">No transactions yet.</li>
        @endforelse
    </ul>
@endsection
