@extends('layouts.app')

@section('title', $student->full_name . ' - Student Details')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Student Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('students.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back to Students
        </a>
        <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning me-2">
            <i class="fas fa-edit me-1"></i>Edit Student
        </a>

    </div>
</div>

<div class="row">
    <!-- Left Column: Student Info + Level Progress -->
    <div class="col-lg-6 mb-4">
        <!-- Student Information Card -->
        <div class="card shadow h-100 mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Student Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-2 mb-1">{{ $student->full_name }}</h5>
                    <p class="text-muted mb-0">{{ $student->username }}</p>
                </div>
                <hr>
                <div class="row mb-2">
                    <div class="col-4"><strong>School:</strong></div>
                    <div class="col-8">{{ $student->school ?? 'N/A' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Class:</strong></div>
                    <div class="col-8">{{ $student->class ?? 'N/A' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-4"><strong>Class Type:</strong></div>
                    <div class="col-8">{{ $student->class_type ?? 'N/A' }}</div>
                </div>
            </div>
        </div>

        <!-- Level Progress Card -->
        <div class="card shadow h-100">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-primary">Level Progress</h6>
            </div>
            <div class="card-body py-3">
                @foreach([1, 2, 3] as $level)
                <div class="mb-2">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-2">
                                @if(isset($levelStats[$level]['unlocked']) && $levelStats[$level]['unlocked'])
                                    <i class="fas fa-unlock text-success"></i>
                                @else
                                    <i class="fas fa-lock text-muted"></i>
                                @endif
                            </div>
                            <div>
                                <small class="mb-0 {{ (isset($levelStats[$level]['unlocked']) && $levelStats[$level]['unlocked']) ? 'text-success' : 'text-muted' }} fw-bold">
                                    Level {{ $level }}
                                </small>
                                <div class="text-muted" style="font-size: 0.75rem;">
                                    {{ $levelStats[$level]['sessions_played'] ?? 0 }} sessions
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            @if(isset($levelStats[$level]['unlocked']) && $levelStats[$level]['unlocked'])
                                <span class="badge bg-success" style="font-size: 0.7rem;">Unlocked</span>
                            @else
                                <span class="badge bg-secondary" style="font-size: 0.7rem;">Locked</span>
                            @endif
                        </div>
                    </div>
                    @if(isset($levelStats[$level]['unlocked']) && $levelStats[$level]['unlocked'])
                    <div class="mt-1">
                        <div class="row text-center" style="font-size: 0.75rem;">
                            <div class="col-4">
                                <div class="text-muted">High</div>
                                <strong>{{ $levelStats[$level]['highest_score'] ?? 0 }}</strong>
                            </div>
                            <div class="col-4">
                                <div class="text-muted">Avg</div>
                                <strong>{{ number_format($levelStats[$level]['average_score'] ?? 0, 1) }}</strong>
                            </div>
                            <div class="col-4">
                                <div class="text-muted">Done</div>
                                <strong>{{ $levelStats[$level]['completed_sessions'] ?? 0 }}</strong>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @if($level < 3)
                    <hr class="my-2">
                @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- Right Column: Game Sessions Card -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Game Sessions</h6>
                <a href="{{ route('games.student-sessions', $student->id) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-gamepad me-1"></i>View All Sessions
                </a>
            </div>
            <div class="card-body">
                @if($student->gameSessions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Session ID</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Score</th>
                                    <th>Started</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $sortedSessions = $student->gameSessions->sortByDesc('started_at')->take(5);
                                @endphp
                                @foreach($sortedSessions as $session)
                                <tr>
                                    <td>
                                        <code>{{ $session->session_id }}</code>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $session->level == 1 ? 'primary' : ($session->level == 2 ? 'success' : 'warning') }}">
                                            L{{ $session->level }}
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
                                    <td>{{ $session->started_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('games.show', $session->id) }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gamepad fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No game sessions</h5>
                        <p class="text-muted">This student hasn't played any games yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection 