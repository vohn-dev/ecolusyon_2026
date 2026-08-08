<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'EcoLusyon') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/ecolusyon.css') }}">
    @stack('styles')
</head>
<body style="background: var(--paper); min-height:100vh;">
    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height:100vh;">
        <a href="{{ route('dashboard') }}" class="text-decoration-none mb-3">
            <span class="h4" style="font-family:serif;color:var(--ink);">🌱 EcoLusyon</span>
        </a>

        <div class="card p-4" style="width:100%; max-width:420px; border-radius:16px;">
            {{ $slot }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
