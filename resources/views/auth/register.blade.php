<x-guest-layout>
    <div class="container" style="max-width:420px;margin-top:60px;">
        <div class="text-center mb-4">
            <h1 class="h3" style="font-family:serif;">Join EcoLusyon</h1>
            <p class="text-muted small">Know → Report → Connect → Earn</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-control" required autofocus>
                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-control" required>
                @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile number</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="09XXXXXXXXX" required>
                @error('phone') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Barangay</label>
                <select name="barangay_id" class="form-select" required>
                    <option value="">Select your barangay</option>
                    @foreach(\App\Models\Barangay::orderBy('name')->get() as $b)
                        <option value="{{ $b->id }}" @selected(old('barangay_id') == $b->id)>
                            {{ $b->name }}, {{ $b->city }}
                        </option>
                    @endforeach
                </select>
                @error('barangay_id') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn w-100" style="background:#2F7D5F;color:#fff;">
                Create account
            </button>

            <p class="text-center small text-muted mt-3">
                Already registered? <a href="{{ route('login') }}">Log in</a>
            </p>
        </form>
    </div>
</x-guest-layout>
