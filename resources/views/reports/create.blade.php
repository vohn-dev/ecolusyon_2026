@extends('layouts.resident')
 
@section('content')
    <h1 class="h5 mb-3">Report Flood-Waste Hotspot</h1>
 
    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data">
        @csrf
 
        <div class="mb-3">
            <label class="form-label small">Photo</label>
            <x-camera-capture name="photo" />
        </div>
 
        <div class="row mb-3">
            <div class="col">
                <label class="form-label small">Latitude</label>
                <input type="text" name="latitude" id="latitude" class="form-control" required>
            </div>
            <div class="col">
                <label class="form-label small">Longitude</label>
                <input type="text" name="longitude" id="longitude" class="form-control" required>
            </div>
        </div>
        <button type="button" class="btn btn-outline-secondary btn-sm mb-3" onclick="useMyLocation()">
            <i class="bi bi-geo-alt"></i> Use my current location
        </button>
 
        <div class="mb-3">
            <label class="form-label small">Severity</label>
            <select name="severity" class="form-select" required>
                <option value="minor">Minor accumulation</option>
                <option value="partial_blockage">Partial blockage</option>
                <option value="full_blockage">Full blockage</option>
            </select>
        </div>
 
        <div class="mb-3">
            <label class="form-label small">Waste types observed</label>
            @foreach(['plastic_bags' => 'Plastic bags', 'sachets' => 'Sachets', 'construction_debris' => 'Construction debris', 'organic_matter' => 'Organic matter'] as $val => $label)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="waste_types[]" value="{{ $val }}" id="wt-{{ $val }}">
                    <label class="form-check-label small" for="wt-{{ $val }}">{{ $label }}</label>
                </div>
            @endforeach
        </div>
 
        <button class="btn w-100" style="background:var(--bantay);color:#fff;">Submit report</button>
    </form>
 
    @push('scripts')
    <script src="{{ asset('js/camera-capture.js') }}"></script>
    <script>
    function useMyLocation() {
        navigator.geolocation.getCurrentPosition(pos => {
            document.getElementById('latitude').value = pos.coords.latitude;
            document.getElementById('longitude').value = pos.coords.longitude;
        });
    }
    </script>
    @endpush
@endsection
