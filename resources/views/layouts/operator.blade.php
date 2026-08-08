<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'EcoLusyon — Operator' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/ecolusyon.css') }}">
    <style>
        body{padding-bottom:78px;}
        .topbar{background:var(--ink);color:#EDEFE6;}
        .shop-pill{background:var(--junk-tint);color:var(--junk);border-radius:999px;padding:4px 12px;font-weight:600;font-size:13px;}
        .tabbar{position:fixed;bottom:0;left:0;right:0;background:#fff;border-top:1px solid var(--line);
                display:flex;justify-content:space-around;padding:8px 0;z-index:40;}
        .tabbar a{color:var(--ink-soft);text-decoration:none;font-size:11px;text-align:center;flex:1;}
        .tabbar a.active{color:var(--junk);font-weight:600;}
        .tabbar i{display:block;font-size:19px;margin-bottom:2px;}
    </style>
</head>
<body>
    <div class="topbar d-flex align-items-center justify-content-between px-3 py-2">
        <a href="{{ route('operator.dashboard') }}" class="text-white text-decoration-none fw-semibold">EcoLusyon</a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('operator.notifications.index') }}" class="text-white position-relative" title="Notifications">
                <i class="bi bi-bell fs-5"></i>
                @isset($unreadCount)
                    @if($unreadCount > 0)
                        <span class="badge rounded-pill bg-danger position-absolute top-0 start-100 translate-middle" style="font-size:9px;">{{ $unreadCount }}</span>
                    @endif
                @endisset
            </a>
            <a href="{{ route('operator.analytics.index') }}" class="text-white" title="Analytics"><i class="bi bi-bar-chart fs-5"></i></a>
            <span class="shop-pill d-none d-sm-inline">{{ auth()->user()->junkshop->name ?? '' }}</span>
        </div>
    </div>

    <main class="container py-3" style="max-width:480px;">
        @if (session('status'))
            <div class="alert alert-success py-2">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>

    <nav class="tabbar">
        <a href="{{ route('operator.dashboard') }}" class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
            <i class="bi bi-house"></i>Home
        </a>
        <a href="{{ route('operator.requests.index') }}" class="{{ request()->routeIs('operator.requests.*') ? 'active' : '' }}">
            <i class="bi bi-inbox"></i>Requests
        </a>
        <a href="{{ route('operator.prices.edit') }}" class="{{ request()->routeIs('operator.prices.*') ? 'active' : '' }}">
            <i class="bi bi-currency-exchange"></i>Prices
        </a>
        <a href="{{ route('operator.transactions.index') }}" class="{{ request()->routeIs('operator.transactions.*') ? 'active' : '' }}">
            <i class="bi bi-journal-text"></i>Log
        </a>
        <a href="{{ route('operator.profile.edit') }}" class="{{ request()->routeIs('operator.profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i>Profile
        </a>
    </nav>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
