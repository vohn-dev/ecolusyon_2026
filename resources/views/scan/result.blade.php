@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Waste Result</h1>

    @php
        $categoryBadge = [
            'biodegradable' => 'badge-tapon',
            'recyclable' => 'badge-junk',
            'residual' => 'badge-bantay',
            'special_hazardous' => 'badge-hazard',
            'e_waste' => 'badge-siklo',
        ][$wasteScan->ai_classification] ?? 'badge-tapon';

        $confidence = $wasteScan->ai_confidence_score;
        $confidenceColor = $confidence >= 70 ? 'bg-success' : ($confidence >= 45 ? 'bg-warning' : 'bg-danger');
    @endphp

    <div class="card p-3 mb-3 text-center">
        <img src="{{ Storage::url($wasteScan->photo_path) }}" class="rounded mb-3" style="max-height:180px;object-fit:cover;">

        <span class="badge rounded-pill {{ $categoryBadge }} px-3 py-2 mx-auto mb-2" style="font-size:.95rem;">
            {{ str_replace('_', ' ', $wasteScan->ai_classification) }}
        </span>
        <div class="small text-muted mb-3">{{ $wasteScan->item_description }}</div>

        <!-- AI confidence as a progress bar -->
        <div class="d-flex justify-content-between small text-muted mb-1">
            <span>AI confidence</span>
            <span>{{ $confidence }}%</span>
        </div>
        <div class="progress" role="progressbar" aria-valuenow="{{ $confidence }}" aria-valuemin="0" aria-valuemax="100" style="height:8px;">
            <div class="progress-bar {{ $confidenceColor }}" style="width: {{ $confidence }}%;"></div>
        </div>
    </div>

    @if($isLowConfidence)
        <div class="alert alert-warning small">
            Confidence is below {{ config('ecolusyon.ai_confidence_threshold') }}% --- please confirm the category.
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
        <div class="d-flex gap-2">
            <a href="{{ route('scan.guide', $wasteScan) }}" class="btn flex-grow-1" style="background:var(--tapon);color:#fff;">
                View disposal guide
            </a>
            <a href="{{ route('scan.retake', $wasteScan) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-repeat"></i> Not right? Fix
            </a>
        </div>
    @endif
@endsection
