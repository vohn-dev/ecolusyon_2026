<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'EcoLusyon') }}</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <link rel="stylesheet" href="{{ asset('css/ecolusyon.css') }}">

    <style>
        body {
            background: #f8f9fa;
            padding-bottom: 76px; /* space for fixed tabbar */
        }

        .tabbar {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1030;
            display: flex;
            align-items: stretch;
            background: #fff;
            border-top: 1px solid #dee2e6;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .tabbar a,
        .tabbar-btn {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .15rem;
            padding: .65rem .25rem;
            border: 0;
            background: transparent;
            color: #6c757d;
            text-decoration: none;
            font-size: .78rem;
            line-height: 1.1;
        }

        .tabbar a.active,
        .tabbar-btn:hover {
            color: #198754;
            font-weight: 600;
        }

        .tabbar i {
            font-size: 1.15rem;
        }

        .tabbar form {
            flex: 1;
            display: flex;
        }
    </style>

    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#122019">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
</head>
<body>

<main class="container py-3" style="max-width:480px;">
    @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif

    {{ $slot }}
    @yield('content')
</main>

<nav class="tabbar">
    <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="bi bi-house"></i>Home
    </a>

    <a href="{{ route('scan.create') }}" class="{{ request()->routeIs('scan.*') ? 'active' : '' }}">
        <i class="bi bi-camera"></i>Scan
    </a>

    <a href="{{ route('reports.create') }}" class="{{ request()->routeIs('reports.*') ? 'active' : '' }}">
        <i class="bi bi-exclamation-triangle"></i>Report
    </a>

    <a href="{{ route('market.index') }}" class="{{ request()->routeIs('market.*') ? 'active' : '' }}">
        <i class="bi bi-recycle"></i>Market
    </a>

    <a href="{{ route('rewards.index') }}" class="{{ request()->routeIs('rewards.*') ? 'active' : '' }}">
        <i class="bi bi-star"></i>Rewards
    </a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js').catch((err) => {
                console.warn('Service worker registration failed:', err);
            });
        });
    }
</script>

</body>
</html>

