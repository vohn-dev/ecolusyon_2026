@extends('layouts.operator')

@section('content')
    <h1 class="h5 mb-1">Materials &amp; pricing</h1>
    <p class="text-muted small mb-3">Visible to residents on JunkConnect</p>

    <form method="POST" action="{{ route('operator.prices.update') }}">
        @csrf
        @method('PUT')

        @foreach($materials as $m)
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span class="text-capitalize">{{ str_replace('_',' ', $m) }}</span>
                @if($m === 'e_waste')
                    <span class="small text-muted">by item</span>
                @else
                    <div class="input-group input-group-sm" style="width:140px;">
                        <span class="input-group-text">₱</span>
                        <input type="number" step="0.01" min="0" name="prices[{{ $m }}]"
                               value="{{ old('prices.'.$m, $prices[$m]->price_per_kg ?? '') }}"
                               class="form-control" placeholder="0.00">
                        <span class="input-group-text">/kg</span>
                    </div>
                @endif
            </div>
        @endforeach

        <button type="submit" class="btn w-100 mt-3 mb-4" style="background:var(--junk);color:#fff;">Save price list</button>
    </form>

    <h2 class="h6">Market benchmark</h2>
    @foreach($benchmarks as $material => [$low, $high])
        @php $mine = $prices[$material]->price_per_kg ?? null; @endphp
        <div class="card p-3 mb-2">
            <div class="small text-muted mb-1">Metro Manila average · {{ str_replace('_',' ', $material) }}</div>
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">₱{{ number_format($low, 2) }} – ₱{{ number_format($high, 2) }}/kg</span>
                @if($mine !== null)
                    @if($mine >= $low && $mine <= $high)
                        <span class="badge-siklo px-2 py-1 rounded small">Your price competitive</span>
                    @else
                        <span class="badge-hazard px-2 py-1 rounded small">Outside benchmark range</span>
                    @endif
                @endif
            </div>
        </div>
    @endforeach
@endsection
