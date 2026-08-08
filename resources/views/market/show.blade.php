@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-1">{{ $junkshop->name }}</h1>
    <p class="text-muted small">{{ $junkshop->address }} · {{ $junkshop->operating_hours }}</p>

    <h2 class="h6">Current prices</h2>
    <ul class="list-group mb-3">
        @foreach($junkshop->materialPrices as $p)
            <li class="list-group-item d-flex justify-content-between">
                <span class="text-capitalize">{{ str_replace('_',' ', $p->material_type) }}</span>
                <span>₱{{ number_format($p->price_per_kg, 2) }}/kg</span>
            </li>
        @endforeach
    </ul>

    <h2 class="h6">Schedule pickup / drop-off</h2>
    <form method="POST" action="{{ route('market.schedule', $junkshop) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label small">Material</label>
            <select name="material_type" class="form-select" required>
                @foreach($junkshop->materialPrices as $p)
                    <option value="{{ $p->material_type }}">{{ str_replace('_',' ', $p->material_type) }} — ₱{{ $p->price_per_kg }}/kg</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label small">Estimated weight (kg)</label>
            <input type="number" step="0.1" min="0.1" name="weight_kg" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_ewaste" value="1" id="is_ewaste">
            <label class="form-check-label small" for="is_ewaste">This is e-waste</label>
        </div>
        <button class="btn w-100" style="background:var(--junk);color:#fff;">Schedule pickup</button>
    </form>
@endsection
