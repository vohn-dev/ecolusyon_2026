<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EcoLusyon' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/ecolusyon.css') }}">
    @stack('styles')
    <style>
        body{padding-bottom:78px;}
        .topbar{background:var(--ink);color:#EDEFE6;}
        .points-pill{background:var(--siklo-tint);color:var(--siklo);border-radius:999px;padding:4px 12px;font-weight:600;font-size:13px;}
        .tabbar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid var(--line);
                display:flex;justify-content:space-around;padding:8px 0;z-index:40;}
        .tabbar a{color:var(--ink-soft);text-decoration:none;font-size:11px;text-align:center;flex:1;}
        .tabbar a.active{color:var(--siklo);font-weight:600;}
        .tabbar i{display:block;font-size:19px;margin-bottom:2px;}
        .tabbar-btn {
            background: none;
            border: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font: inherit;
            cursor: pointer;
            width: 100%;
            height: 100%;
            padding: 0;
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
    <div class="topbar d-flex align-items-center justify-content-between px-3 py-2">
        <a href="{{ route('dashboard') }}" class="text-white text-decoration-none fw-semibold">EcoLusyon</a>
        <div class="d-flex align-items-center gap-2">
            <span class="points-pill">{{ auth()->user()->current_points }} pts</span>
            <a href="{{ route('profile.edit') }}" class="text-white"><i class="bi bi-person-circle fs-5"></i></a>
        </div>
    </div>

    <main class="container py-3" style="max-width:480px;">
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
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
