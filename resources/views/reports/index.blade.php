@extends('layouts.resident')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h5 mb-0">My Reports</h1>
        <a href="{{ route('reports.create') }}" class="btn btn-sm" style="background:var(--bantay);color:#fff;">+ New</a>
    </div>

    @php
        $statusColor = [
            'submitted' => 'secondary', 'verified' => 'info', 'dispatched' => 'primary',
            'cleaned' => 'success', 'confirmed' => 'success', 'rejected' => 'danger',
        ];
        $severityBadge = ['minor' => 'warning', 'partial_blockage' => 'orange', 'full_blockage' => 'danger'];
    @endphp

    <ul class="list-group">
        @forelse($reports as $report)
            <li class="list-group-item">
                <div class="d-flex justify-content-between">
                    <span class="text-capitalize">{{ str_replace('_', ' ', $report->severity) }}</span>
                    <span class="badge bg-{{ $statusColor[$report->status] }} text-capitalize">{{ $report->status }}</span>
                </div>
                <div class="small text-muted">{{ $report->created_at->diffForHumans() }}</div>

                @if($report->status === 'cleaned')
                    <form method="POST" action="{{ route('reports.verify-cleanup', $report) }}" class="mt-2">
                        @csrf
                        <button class="btn btn-sm btn-outline-success">Verify cleanup (+5 pts)</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="list-group-item text-muted small">No reports yet.</li>
        @endforelse
    </ul>
@endsection
