@extends('layouts.resident')

@section('content')
    <h1 class="h5 mb-3">Barangay Leaderboard</h1>
    <ul class="list-group">
        @foreach($top as $i => $u)
            <li class="list-group-item d-flex justify-content-between">
                <span>{{ $i + 1 }}. {{ $u->name }}</span>
                <span class="fw-semibold">{{ number_format($u->total_points) }} pts</span>
            </li>
        @endforeach
    </ul>
@endsection
