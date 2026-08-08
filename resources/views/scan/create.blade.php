@extends('layouts.resident')
 
@section('content')
    <h1 class="h5 mb-3">Scan Waste</h1>
    <p class="text-muted small">Take or upload a photo. TaponWise will identify it and tell you where it goes.</p>
 
    <form method="POST" action="{{ route('scan.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="card p-4 text-center mb-3" style="border-style:dashed;">
            <x-camera-capture name="photo" />
        </div>
        <button class="btn w-100" style="background:var(--tapon);color:#fff;">Classify item</button>
    </form>
 
    @push('scripts')
        <script src="{{ asset('js/camera-capture.js') }}"></script>
    @endpush
@endsection
resources/views/scan/result.blade.php:
@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Waste Result</h1>

    <div class="card p-3 mb-3 text-center badge-tapon">
        <img src="{{ Storage::url($wasteScan->photo_path) }}" class="rounded mb-2" style="max-height:180px;object-fit:cover;">
        <div class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $wasteScan->ai_classification) }}</div>
        <div class="small text-muted">{{ $wasteScan->item_description }}</div>
        <div class="small mt-1">Confidence: {{ $wasteScan->ai_confidence_score }}%</div>
    </div>

    @if($isLowConfidence)
        <div class="alert alert-warning small">
            Confidence is below {{ config('ecolusyon.ai_confidence_threshold') }}% — please confirm the category.
        </div>
        <form method="POST" action="{{ route('scan.confirm-category', $wasteScan) }}">
            @csrf
            <div class="mb-3">
                @foreach(['biodegradable','recyclable','residual','special_hazardous','e_waste'] as $cat)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="category" value="{{ $cat }}"
                               id="cat-{{ $cat }}" @checked($cat === $wasteScan->ai_classification) required>
                        <label class="form-check-label text-capitalize" for="cat-{{ $cat }}">
                            {{ str_replace('_', ' ', $cat) }}
                        </label>
                    </div>
                @endforeach
            </div>
            <button class="btn w-100" style="background:var(--tapon);color:#fff;">Confirm category</button>
        </form>
    @else
        <a href="{{ route('scan.guide', $wasteScan) }}" class="btn w-100" style="background:var(--tapon);color:#fff;">
            View disposal guide
        </a>
    @endif
@endsection
resources/views/scan/guide.blade.php:
@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Disposal Guide</h1>

    @php
        $tips = [
            'biodegradable' => 'Compost at home or place in your barangay\'s green bin.',
            'recyclable' => 'Rinse and bring to a junkshop or MRF for the best value.',
            'residual' => 'Bag securely and place out on your barangay\'s residual collection day.',
            'special_hazardous' => 'Do not mix with regular trash — bring to an accredited TSD facility.',
            'e_waste' => 'Never give to informal collectors — route to an accredited TSD facility (DAO 2024-1655).',
        ];
    @endphp

    <div class="card p-3 mb-3">
        <div class="fw-semibold text-capitalize mb-1">{{ str_replace('_', ' ', $wasteScan->ai_classification) }}</div>
        <p class="small mb-2">{{ $tips[$wasteScan->ai_classification] }}</p>

        @if($barangay)
            <div class="small text-muted">
                Next collection day in {{ $barangay->name }}:
                {{ $barangay->collection_schedule[$wasteScan->ai_classification === 'biodegradable' ? 'biodegradable' : ($wasteScan->ai_classification === 'residual' ? 'residual' : 'recyclable')] ?? 'Check with your barangay' }}
            </div>
        @endif
    </div>

    @if($nearbyJunkshops->isNotEmpty())
        <h2 class="h6">Nearby drop-off points</h2>
        <ul class="list-group mb-3">
            @foreach($nearbyJunkshops as $shop)
                <li class="list-group-item">
                    <div class="fw-semibold">{{ $shop->name }}</div>
                    <div class="small text-muted">{{ $shop->address }}</div>
                    <a href="{{ route('market.show', $shop) }}" class="small">View details →</a>
                </li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('scan.confirm-disposal', $wasteScan) }}">
        @csrf
        <button class="btn w-100 {{ $wasteScan->disposal_confirmed ? 'btn-secondary disabled' : '' }}"
                style="{{ $wasteScan->disposal_confirmed ? '' : 'background:var(--tapon);color:#fff;' }}">
            {{ $wasteScan->disposal_confirmed ? 'Disposal already confirmed' : 'Confirm disposal (+10 pts)' }}
        </button>
    </form>
@endsection
