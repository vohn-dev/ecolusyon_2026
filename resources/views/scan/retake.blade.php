@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Retake photo</h1>
    <p class="text-muted small">Not the right read? Take or upload a clearer photo and we'll reclassify it.</p>

    <form method="POST" action="{{ route('scan.retake', $wasteScan) }}" enctype="multipart/form-data">
        @csrf
        <div class="card p-4 text-center mb-3" style="border-style:dashed;">
            <x-camera-capture name="photo" />
        </div>
        <button class="btn w-100" style="background:var(--tapon);color:#fff;">Reclassify item</button>
    </form>

    @push('scripts')
        <script src="{{ asset('js/camera-capture.js') }}"></script>
    @endpush
@endsection
