<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h4" style="font-family:serif;">Welcome back</h1>
        <p class="text-muted small">Know &rarr; Report &rarr; Connect &rarr; Earn</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success py-2 small">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
            @error('email') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password') <div class="text-danger small">{{ $message }}</div> @enderror
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label small" for="remember">Remember me</label>
        </div>

        <button type="submit" class="btn w-100" style="background:#2F7D5F;color:#fff;">Log in</button>

        <div class="text-center small mt-3">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot your password?</a><br>
            @endif
            Don't have an account? <a href="{{ route('register') }}">Register</a>
        </div>
    </form>
</x-guest-layout>
