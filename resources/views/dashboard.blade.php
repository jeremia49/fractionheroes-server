@extends('layouts.app')

@section('title', 'Dashboard - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
            <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('students.create') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-plus me-1"></i>Add Student
                </a>
                <a href="{{ route('games.index') }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-gamepad me-1"></i>Game Sessions
                </a>
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
                            Total Students</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalStudents }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            Game Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalGameSessions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-gamepad fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Game Statistics Row -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Active Sessions</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeGameSessions }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Captured Faces</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalFaces }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-camera fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Avg Game Score</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($averageGameScore, 1) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-trophy fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row">
    <!-- Recent Students -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Students</h6>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Class</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentStudents as $student)
                                <tr>
                                    <td>
                                        <a href="{{ route('students.show', $student->id) }}" class="text-decoration-none">
                                            {{ $student->full_name }}
                                        </a>
                                    </td>
                                    <td>{{ $student->username }}</td>
                                    <td>{{ $student->class ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No students found.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Game Sessions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Recent Game Sessions</h6>
                <a href="{{ route('games.index') }}" class="btn btn-sm btn-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentGameSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Student</th>
                                    <th>Score</th>
                                    <th>Status</th>
                                    <th>Started</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentGameSessions as $session)
                                <tr>
                                    <td>
                                        <a href="{{ route('students.show', $session->student->id) }}" class="text-decoration-none">
                                            {{ $session->student->full_name }}
                                        </a>
                                    </td>
                                    <td>
                                        <strong>{{ $session->total_score }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $session->game_status === 'completed' ? 'success' : ($session->game_status === 'active' ? 'primary' : 'secondary') }}">
                                            {{ ucfirst($session->game_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $session->started_at->format('M d, H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center">No game sessions found.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 