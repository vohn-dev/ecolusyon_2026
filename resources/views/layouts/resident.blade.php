<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EcoLusyon' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/ecolusyon.css') }}">
    <style>
        body{padding-bottom:78px;}
        .topbar{background:var(--ink);color:#EDEFE6;}
        .points-pill{background:var(--siklo-tint);color:var(--siklo);border-radius:999px;padding:4px 12px;font-weight:600;font-size:13px;}
        .tabbar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid var(--line);
                display:flex;justify-content:space-around;padding:8px 0;z-index:40;}
        .tabbar a{color:var(--ink-soft);text-decoration:none;font-size:11px;text-align:center;flex:1;}
        .tabbar a.active{color:var(--siklo);font-weight:600;}
        .tabbar i{display:block;font-size:19px;margin-bottom:2px;}
    </style>
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
            <div class="alert alert-success py-2">{{ session('status') }}</div>
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
</body>
</html>
