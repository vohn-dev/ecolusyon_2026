<x-guest-layout>
    <div class="container" style="max-width:420px;margin-top:60px;">
        <div class="text-center mb-4">
            <h1 class="h3" style="font-family:serif;">List your junkshop</h1>
            <p class="text-muted small">Reach more households · fair, transparent prices</p>
        </div>

        <form method="POST" action="{{ route('operator.register') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">Your name</label>
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
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
                @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn w-100" style="background:var(--junk);color:#fff;">
                Create operator account
            </button>

            <p class="text-center small text-muted mt-3">
                Already have an account? <a href="{{ route('login') }}">Log in</a><br>
                Signing up as a household instead? <a href="{{ route('register') }}">Register here</a>
            </p>
        </form>
    </div>
</x-guest-layout>
