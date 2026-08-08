@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Report Flood-Waste Hotspot</h1>

    <form method="POST" action="{{ route('reports.store') }}" enctype="multipart/form-data" id="flood-report-form">
        @csrf

        <div class="mb-3">
            <label class="form-label small">Photo</label>
            <x-camera-capture name="photo" />
        </div>

        <button type="button" class="btn btn-outline-secondary btn-sm mb-2 w-100" id="auto-detect-btn">
            <i class="bi bi-stars"></i> Auto-detect with AI
        </button>
        <div id="auto-detect-status" class="small text-muted mb-3"></div>

        <input type="hidden" name="latitude" id="latitude" required>
        <input type="hidden" name="longitude" id="longitude" required>

        <button type="button" class="btn btn-outline-secondary btn-sm mb-2" onclick="useMyLocation()">
            <i class="bi bi-geo-alt"></i> Use my current location
        </button>
        <div id="address-display" class="small mb-3" style="display:none;">
            <i class="bi bi-geo-alt-fill text-danger"></i> <span id="address-text"></span>
        </div>

        <div class="mb-3">
            <label class="form-label small d-block">Severity</label>
            <div class="btn-group w-100" role="group" aria-label="Severity">
                @foreach(['minor' => 'Minor', 'partial_blockage' => 'Partial', 'full_blockage' => 'Full blockage'] as $val => $label)
                    <input type="radio" class="btn-check" name="severity" id="sev-{{ $val }}" value="{{ $val }}" required>
                    <label class="btn btn-outline-secondary btn-sm" for="sev-{{ $val }}">{{ $label }}</label>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label small d-block">Waste types observed</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach(['plastic_bags' => 'Plastic bags', 'sachets' => 'Sachets', 'construction_debris' => 'Construction debris', 'organic_matter' => 'Organic matter'] as $val => $label)
                    <input type="checkbox" class="btn-check" name="waste_types[]" id="wt-{{ $val }}" value="{{ $val }}" autocomplete="off">
                    <label class="btn btn-outline-secondary btn-sm rounded-pill" for="wt-{{ $val }}">{{ $label }}</label>
                @endforeach
            </div>
        </div>

        <button class="btn w-100" style="background:var(--tapon);color:#fff;">Submit report</button>
    </form>

    @push('scripts')
    <script src="{{ asset('js/camera-capture.js') }}"></script>
    <script>
    function useMyLocation() {
        navigator.geolocation.getCurrentPosition(async (pos) => {
            document.getElementById('latitude').value = pos.coords.latitude;
            document.getElementById('longitude').value = pos.coords.longitude;

            const addressWrap = document.getElementById('address-display');
            const addressText = document.getElementById('address-text');
            addressText.textContent = 'Locating address…';
            addressWrap.style.display = 'block';

            try {
                const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${pos.coords.latitude}&lon=${pos.coords.longitude}`);
                const data = await res.json();
                addressText.textContent = data.display_name ?? `${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}`;
            } catch (err) {
                addressText.textContent = `${pos.coords.latitude.toFixed(5)}, ${pos.coords.longitude.toFixed(5)}`;
            }
        }, () => {
            alert("Couldn't get your location — check your browser's location permission.");
        });
    }

    document.getElementById('auto-detect-btn').addEventListener('click', async () => {
        const fileInput = document.querySelector('.camera-capture__fileinput');
        const status = document.getElementById('auto-detect-status');

        if (!fileInput.files.length) {
            status.textContent = 'Take or upload a photo first, then auto-detect.';
            return;
        }

        status.textContent = 'Analyzing photo…';

        const formData = new FormData();
        formData.append('photo', fileInput.files[0]);

        const res = await fetch('{{ route('reports.auto-detect') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('input[name=_token]').value },
            body: formData,
        });

        if (!res.ok) {
            status.textContent = "Couldn't analyze that photo — pick severity and waste types manually.";
            return;
        }

        const result = await res.json();

        document.getElementById(`sev-${result.severity}`).checked = true;
        document.querySelectorAll('input[name="waste_types[]"]').forEach((el) => {
            el.checked = result.waste_types.includes(el.value);
        });

        status.textContent = `Detected: ${result.severity.replace('_', ' ')} (${result.confidence}% confidence) — review and adjust if needed.`;
    });
    </script>
    @endpush
@endsection
