@extends('layouts.app')

@section('title', 'Student Game History - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Game History - {{ $student->full_name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('students.show', $student->id) }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back to Student
        </a>
        <a href="{{ route('games.index') }}" class="btn btn-info">
            <i class="fas fa-gamepad me-1"></i>All Sessions
        </a>
    </div>
</div>

<!-- Student Information -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">{{ $student->full_name }}</h5>
                        <p class="card-text text-muted mb-0">
                            <strong>School:</strong> {{ $student->school }} | 
                            <strong>Class:</strong> {{ $student->class }} | 
                            <strong>Type:</strong> {{ $student->class_type }}
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <span class="badge bg-primary fs-6">Student ID: {{ $student->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gamepad fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Completed Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_sessions'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_score'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Average Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['average_score'], 1) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Level Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Level Progress</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach([1, 2, 3] as $level)
                    <div class="col-md-4 mb-3">
                        <div class="card border-left-{{ $level == 1 ? 'primary' : ($level == 2 ? 'success' : 'warning') }} shadow h-100">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-{{ $level == 1 ? 'primary' : ($level == 2 ? 'success' : 'warning') }} text-uppercase mb-1">
                                            Level {{ $level }}
                                            @if($levelStats[$level]['unlocked'])
                                                <i class="fas fa-unlock text-success ms-1"></i>
                                            @else
                                                <i class="fas fa-lock text-muted ms-1"></i>
                                            @endif
                                        </div>
                                        <div class="h6 mb-1 font-weight-bold text-gray-800">
                                            {{ $levelStats[$level]['sessions_played'] }} Sessions
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Highest: {{ $levelStats[$level]['highest_score'] }} pts
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Avg: {{ number_format($levelStats[$level]['average_score'], 1) }} pts
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Completed: {{ $levelStats[$level]['completed_sessions'] }}
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-layer-group fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Game Sessions Table -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Game Sessions History</h6>
    </div>
    <div class="card-body">
        @if($gameSessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Started</th>
                            <th>Faces</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($gameSessions as $session)
                        <tr>
                            <td>
                                <code>{{ $session->session_id }}</code>
                            </td>
                            <td>
                                <span class="badge bg-{{ $session->level == 1 ? 'primary' : ($session->level == 2 ? 'success' : 'warning') }}">
                                    Level {{ $session->level }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $session->game_status === 'completed' ? 'success' : ($session->game_status === 'active' ? 'primary' : 'secondary') }}">
                                    {{ ucfirst($session->game_status) }}
                                </span>
                            </td>
                            <td>
                                <strong>{{ $session->total_score }}</strong>
                                @if($session->completed_rounds > 0)
                                    <small class="text-muted">({{ number_format($session->average_score, 1) }} avg)</small>
                                @endif
                            </td>
                            <td>
                                @if($session->duration)
                                    {{ gmdate('H:i:s', $session->duration) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $session->started_at->format('M d, Y H:i') }}</td>
                            <td>
                                <span class="badge bg-info">{{ $session->faces->count() }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('games.show', $session->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($session->faces->count() > 0)
                                    <a href="{{ route('games.show', $session->id) }}#faces" 
                                       class="btn btn-sm btn-outline-secondary" title="View Faces">
                                        <i class="fas fa-camera"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $gameSessions->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-gamepad fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No game sessions found</h5>
                <p class="text-muted">This student hasn't played any games yet.</p>
            </div>
        @endif
    </div>
</div>

<!-- Performance Chart -->
@if($gameSessions->count() > 1)
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Performance Over Time</h6>
            </div>
            <div class="card-body">
                <canvas id="performanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
@if($gameSessions->count() > 1)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const sessions = @json($gameSessions->reverse()->values());
    const labels = sessions.map(s => s.started_at->format('M d'));
    const scores = sessions.map(s => s.total_score);
    const averages = sessions.map(s => s.average_score || 0);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Score',
                data: scores,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Average Score',
                data: averages,
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endif
@endpush 