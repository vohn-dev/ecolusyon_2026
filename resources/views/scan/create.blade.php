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
