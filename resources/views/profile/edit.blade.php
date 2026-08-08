@php
    $name = $user->name ?? auth()->user()->name ?? '';
    $initials = collect(explode(' ', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<style>
    .resident-shell {
        max-width: 480px;
        margin: 0 auto;
    }

    .profile-avatar {
        width: 72px;
        height: 72px;
        border-radius: 18px;
        background: #15261b;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        font-weight: 700;
        letter-spacing: .03em;
        flex: 0 0 auto;
    }

    .section-label {
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .14em;
        text-transform: uppercase;
        color: #4f5a4c;
        margin: 0 0 .75rem;
    }

    .soft-card {
        border: 1px solid #e6e8da;
        border-radius: 18px;
        background: #fff;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: .78rem 0;
        border-bottom: 1px solid #e8eadf;
    }

    .detail-row:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .detail-label {
        color: #5f665b;
        font-size: .95rem;
    }

    .detail-value {
        font-weight: 700;
        color: #1e271f;
        text-align: right;
    }

    .form-control,
    .form-select {
        border-radius: 14px;
        border-color: #dfe4cf;
        min-height: 48px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #2e5b38;
        box-shadow: 0 0 0 .2rem rgba(46, 91, 56, .12);
    }

    .btn-forest {
        background: #132119;
        border-color: #132119;
        color: #fff;
        border-radius: 16px;
    }

    .btn-forest:hover {
        background: #0f1b14;
        border-color: #0f1b14;
        color: #fff;
    }

    .toggle-item {
        padding: .9rem 0;
        border-bottom: 1px solid #e8eadf;
    }

    .toggle-item:last-child {
        border-bottom: 0;
        padding-bottom: 0;
    }

    .form-switch .form-check-input {
        width: 2.8rem;
        height: 1.45rem;
        border-radius: 999px;
    }

    .form-switch .form-check-input:checked {
        background-color: #2e7d32;
        border-color: #2e7d32;
    }

    .muted-mini {
        font-size: .86rem;
        color: #6b7268;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <div class="resident-shell px-2">
            <h1 class="h5 mb-0 fw-semibold">{{ __('Profile') }}</h1>
            <div class="text-muted small">Manage your resident profile and preferences</div>
        </div>
    </x-slot>

    <div class="resident-shell py-3 px-2">

        <div class="d-flex align-items-start gap-3 mb-4">
            <div class="profile-avatar">
                {{ $initials ?: 'U' }}
            </div>

            <div class="flex-grow-1">
                <div class="h5 mb-1 fw-semibold lh-sm">
                    {{ $user->name ?? auth()->user()->name }}
                </div>

                <div class="muted-mini">
                    Resident • {{ $user->household_address ?? '—' }}
                </div>

                <span class="badge rounded-pill mt-2 px-3 py-2"
                      style="background:#e6f4df; color:#3a7a31; border:1px solid #cde7c4;">
                    ✓ Verified household
                </span>
            </div>
        </div>

        <div class="section-label">Account details</div>
        <div class="soft-card p-3 mb-4">
            <div class="detail-row">
                <div class="detail-label">Mobile number</div>
                <div class="detail-value">{{ $user->mobile_number ?? '—' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Household address</div>
                <div class="detail-value">{{ $user->household_address ?? '—' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Barangay collection sched.</div>
                <div class="detail-value">{{ $user->collection_schedule ?? 'Mon/Thu Residual' }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Member since</div>
                <div class="detail-value">
                    {{ optional($user->created_at)->format('M Y') ?? '—' }}
                </div>
            </div>
        </div>

        <div class="section-label">Edit profile</div>
        <div class="soft-card p-3 mb-4">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Full name</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        autocomplete="name"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="household_address" class="form-label fw-semibold">Household address</label>
                    <input
                        id="household_address"
                        name="household_address"
                        type="text"
                        class="form-control @error('household_address') is-invalid @enderror"
                        value="{{ old('household_address', $user->household_address) }}"
                    >
                    @error('household_address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="mobile_number" class="form-label fw-semibold">Mobile number</label>
                    <input
                        id="mobile_number"
                        name="mobile_number"
                        type="text"
                        class="form-control @error('mobile_number') is-invalid @enderror"
                        value="{{ old('mobile_number', $user->mobile_number) }}"
                        autocomplete="tel"
                    >
                    @error('mobile_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn w-100" style="background:#2F7D5F;color:#fff;">
                    Save changes
                </button>
            </form>
        </div>

        <div class="section-label">Preferences</div>
        <div class="soft-card p-3 mb-4">
            <div class="toggle-item d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-semibold">Push notifications</div>
                    <div class="muted-mini">Collection reminders, dispatch updates</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" checked disabled>
                </div>
            </div>

            <div class="toggle-item d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-semibold">Language</div>
                    <div class="muted-mini">Filipino / English toggle</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" checked disabled>
                </div>
            </div>

            <div class="toggle-item d-flex align-items-center justify-content-between gap-3">
                <div>
                    <div class="fw-semibold">High-contrast mode</div>
                    <div class="muted-mini">Accessibility</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" disabled>
                </div>
            </div>

            <div class="d-flex align-items-center justify-content-between gap-3 pt-3">
                <div>
                    <div class="fw-semibold">Share GPS with reports</div>
                    <div class="muted-mini">Required for BantaYBaba accuracy</div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" checked disabled>
                </div>
            </div>
        </div>

        <div class="section-label">Privacy & data</div>
        <div class="soft-card p-3 mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-4 d-flex align-items-center justify-content-center"
                     style="width:44px;height:44px;background:#e7edf4;color:#6f7f8f;">
                    <i class="bi bi-lock"></i>
                </div>
                <div>
                    <div class="fw-semibold">Data Privacy Act (RA 10173) notice</div>
                    <div class="muted-mini">Consent, photo retention & deletion requests</div>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-outline-dark w-100 fw-semibold rounded-3">
                Log out
            </button>
        </form>
    </div>
</x-app-layout>