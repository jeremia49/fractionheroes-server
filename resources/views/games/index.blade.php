@extends('layouts.app')

@section('title', 'Game Sessions - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Game Sessions</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('games.faces') }}" class="btn btn-info me-2">
            <i class="fas fa-camera me-1"></i>View Faces
        </a>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
        </a>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] ?? 0 }}</div>
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
                            Active Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_sessions'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play fa-2x text-gray-300"></i>
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
                            Total Faces</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_faces'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-camera fa-2x text-gray-300"></i>
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
                        <i class="fas fa-star fa-2x text-gray-300"></i>
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
                <h6 class="m-0 font-weight-bold text-primary">Level Statistics</h6>
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
                                            Level {{ $level }}</div>
                                        <div class="h6 mb-1 font-weight-bold text-gray-800">
                                            {{ $levelStats[$level]['sessions_played'] ?? 0 }} Sessions
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Highest: {{ $levelStats[$level]['highest_score'] ?? 0 }} pts
                                        </div>
                                        <div class="text-xs text-gray-600">
                                            Avg: {{ number_format($levelStats[$level]['average_score'] ?? 0, 1) }} pts
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
        <h6 class="m-0 font-weight-bold text-primary">All Game Sessions</h6>
    </div>
    <div class="card-body">
        @if($gameSessions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>Student</th>
                            <th>Level</th>
                            <th>Status</th>
                            <th>Score</th>
                            <th>Duration</th>
                            <th>Started</th>
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
                                <a href="{{ route('students.show', $session->student->id) }}" class="text-decoration-none">
                                    {{ $session->student->full_name }}
                                </a>
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
                            </td>
                            <td>
                                @if($session->duration)
                                    {{ gmdate('H:i:s', $session->duration) }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $session->started_at ? $session->started_at->format('M d, Y H:i') : '-' }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('games.show', $session->id) }}" 
                                       class="btn btn-sm btn-outline-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('games.student-sessions', $session->student->id) }}" 
                                       class="btn btn-sm btn-outline-secondary" title="Student History">
                                        <i class="fas fa-history"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center">
                <nav aria-label="Game sessions pagination">
                    {{ $gameSessions->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-gamepad fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No game sessions found</h5>
                <p class="text-muted">Game sessions will appear here when students start playing.</p>
            </div>
        @endif
    </div>
</div>
@endsection 