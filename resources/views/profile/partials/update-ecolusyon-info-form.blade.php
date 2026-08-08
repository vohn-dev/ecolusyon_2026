<section>
    <header>
        <h2 class="h5">Resident details</h2>
        <p class="text-muted small">Your barangay determines your collection schedule and nearby drop-off points.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-3">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label class="form-label">Mobile number</label>
            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Barangay</label>
            <select name="barangay_id" class="form-select">
                @foreach(\App\Models\Barangay::orderBy('name')->get() as $b)
                    <option value="{{ $b->id }}" @selected($user->barangay_id === $b->id)>
                        {{ $b->name }}, {{ $b->city }}
                    </option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-sm" style="background:#2F7D5F;color:#fff;">Save</button>
    </form>
</section>
