@extends('layouts.guest-plain')

@section('content')
    <div class="container" style="max-width:480px;margin-top:40px;">
        <div class="text-center mb-4">
            <h1 class="h4" style="font-family:serif;">Register My Junkshop</h1>
            <p class="text-muted small">Households will find you by material and distance once this is saved.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2 small">Please fill in every required field.</div>
        @endif

        <form method="POST" action="{{ route('operator.profile.update') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Shop name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <input type="text" name="address" value="{{ old('address') }}" class="form-control" required>
            </div>

            <div class="row g-2 mb-3">
                <div class="col-6">
                    <label class="form-label">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" value="{{ old('latitude') }}" class="form-control" placeholder="e.g. 14.6547" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" value="{{ old('longitude') }}" class="form-control" placeholder="e.g. 120.9834" required>
                </div>
            </div>
            <p class="small text-muted mt-n2 mb-3">Pin the exact shop location — copy coordinates from Google Maps (long-press the pin) for now; a tap-to-drop map is a good follow-up exercise, same as the interactive map added to TaponWise in the Resident series' revision patch.</p>

            <div class="mb-3">
                <label class="form-label">Operating hours</label>
                <input type="text" name="operating_hours" value="{{ old('operating_hours') }}" class="form-control" placeholder="e.g. 7AM–6PM daily" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Materials accepted</label>
                <div class="row row-cols-2 g-1">
                    @foreach($materials as $m)
                        <div class="col">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="materials_accepted[]" value="{{ $m }}" id="mat-{{ $m }}" @checked(in_array($m, old('materials_accepted', [])))>
                                <label class="form-check-label small" for="mat-{{ $m }}">{{ str_replace('_',' ', $m) }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" name="is_accredited_tsd" value="1" id="is_tsd">
                <label class="form-check-label small" for="is_tsd">
                    We're an accredited e-waste TSD facility (self-declared — subject to LGU verification)
                </label>
            </div>

            <button type="submit" class="btn w-100" style="background:var(--junk);color:#fff;">Submit registration</button>
        </form>
    </div>
@endsection
