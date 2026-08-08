@extends('layouts.operator')

@section('content')
    <h1 class="h5 mb-1">Transaction log</h1>
    <p class="text-muted small mb-3">Every buy builds your income record</p>

    <div class="card p-3 mb-3">
        <div class="fw-semibold mb-2">New transaction</div>

        <form method="POST" action="{{ route('operator.transactions.store') }}">
            @csrf

            @if($acceptedRequests->isNotEmpty())
                <div class="mb-2">
                    <label class="form-label small">From an accepted request (optional)</label>
                    <select name="pickup_request_id" class="form-select form-select-sm" id="pickup-select">
                        <option value="">— Walk-in / not from a request —</option>
                        @foreach($acceptedRequests as $r)
                            <option value="{{ $r->id }}" data-material="{{ $r->material_type }}" data-weight="{{ $r->estimated_weight_kg }}">
                                {{ $r->resident->name }} — {{ str_replace('_',' ', $r->material_type) }} (~{{ $r->estimated_weight_kg }}kg)
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="mb-2">
                <label class="form-label small">Walk-in resident email (optional)</label>
                <input type="email" name="resident_email" class="form-control form-control-sm" placeholder="Leave blank for an anonymous walk-in">
            </div>

            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small">Material</label>
                    <select name="material_type" class="form-select form-select-sm" id="material-select" required>
                        <option value="">Select material</option>
                        @foreach($materials as $type => $pricePerKg)
                            <option value="{{ $type }}">{{ str_replace('_',' ', $type) }} — ₱{{ $pricePerKg }}/kg</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label small">Weight (kg)</label>
                    <input type="number" step="0.1" min="0.1" name="weight_kg" id="weight-input" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-check my-2">
                <input class="form-check-input" type="checkbox" name="is_ewaste" value="1" id="is_ewaste">
                <label class="form-check-label small" for="is_ewaste">This is e-waste</label>
            </div>

            <button type="submit" class="btn btn-sm w-100 mt-2" style="background:var(--junk);color:#fff;">Log transaction</button>
        </form>
    </div>

    <h2 class="h6">This week's income record</h2>
    <div class="row g-2 mb-2">
        <div class="col-6">
            <div class="card p-3 text-center">
                <div class="h5 mb-0">₱{{ number_format($weekTotal, 2) }}</div>
                <div class="small text-muted">Total bought</div>
            </div>
        </div>
        <div class="col-6">
            <div class="card p-3 text-center">
                <div class="h5 mb-0">{{ number_format($weekWeight, 1) }}kg</div>
                <div class="small text-muted">Volume</div>
            </div>
        </div>
    </div>
    <p class="small text-muted mb-3">
        Exportable as income documentation for social-protection applications (DAO 2024-1655) — see <a href="{{ route('operator.analytics.index') }}">Analytics</a>.
    </p>

    <h2 class="h6">Recent transactions</h2>
    <ul class="list-group">
        @forelse($recent as $t)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <div>{{ $t->weight_kg }}kg {{ str_replace('_',' ', $t->material_type) }} — {{ $t->resident->name ?? 'Walk-in' }}</div>
                    <div class="small text-muted">{{ $t->created_at->diffForHumans() }}</div>
                </div>
                <span class="fw-semibold">₱{{ number_format($t->price_total, 2) }}</span>
            </li>
        @empty
            <li class="list-group-item text-muted small">No transactions yet.</li>
        @endforelse
    </ul>

    @push('scripts')
    <script>
        document.getElementById('pickup-select')?.addEventListener('change', function () {
            const opt = this.selectedOptions[0];
            if (!opt.dataset.material) return;
            document.getElementById('material-select').value = opt.dataset.material;
            document.getElementById('weight-input').value = opt.dataset.weight;
        });
    </script>
    @endpush
@endsection
