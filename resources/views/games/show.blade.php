@extends('layouts.app')

@section('title', 'Game Session Details - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Game Session Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('games.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back to Sessions
        </a>
        <a href="{{ route('games.student-sessions', $gameSession->student->id) }}" class="btn btn-info">
            <i class="fas fa-history me-1"></i>Student History
        </a>
    </div>
</div>

<div class="row">
    <!-- Session Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Session Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="fas fa-gamepad fa-2x text-white"></i>
                    </div>
                    <h5 class="mt-2 mb-1">Session {{ $gameSession->session_id }}</h5>
                    <p class="text-muted mb-0">{{ $gameSession->student->full_name }}</p>
                </div>
                
                <hr>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Status:</strong></div>
                    <div class="col-8">
                        <span class="badge
                            @if($gameSession->game_status === 'completed') bg-success
                            @elseif($gameSession->game_status === 'uncompleted') bg-danger
                            @else bg-primary @endif">
                            {{ ucfirst($gameSession->game_status) }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Level:</strong></div>
                    <div class="col-8">
                        <span class="badge bg-{{ $gameSession->level == 1 ? 'primary' : ($gameSession->level == 2 ? 'success' : 'warning') }}">
                            Level {{ $gameSession->level }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Total Score:</strong></div>
                    <div class="col-8">
                        <strong>{{ $gameSession->total_score }}</strong>
                        @if($gameSession->completed_rounds > 0)
                            <small class="text-muted">({{ number_format($gameSession->average_score, 1) }} avg)</small>
                        @endif
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Progress:</strong></div>
                    <div class="col-8">
                        {{ $gameSession->completed_rounds }}
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Started:</strong></div>
                    <div class="col-8">{{ $gameSession->started_at->format('M d, Y H:i:s') }}</div>
                </div>
                
                @if($gameSession->ended_at)
                <div class="row mb-2">
                    <div class="col-4"><strong>Ended:</strong></div>
                    <div class="col-8">{{ $gameSession->ended_at->format('M d, Y H:i:s') }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-4"><strong>Duration:</strong></div>
                    <div class="col-8">{{ gmdate('H:i:s', $gameSession->duration) }}</div>
                </div>
                @endif
                
                @if($gameSession->notes)
                <div class="row mb-2">
                    <div class="col-4"><strong>Notes:</strong></div>
                    <div class="col-8">{{ $gameSession->notes }}</div>
                </div>
                @endif
                
                <!-- Update Notes Form -->
                <hr>
                <form method="POST" action="{{ route('games.update-notes', $gameSession->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add notes about this session...">{{ $gameSession->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Update Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Captured Faces -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Captured Faces ({{ $gameSession->faces->count() }})</h6>
            </div>
            <div class="card-body">
                @if($gameSession->faces->count() > 0)
                    <div class="row">
                        @foreach($gameSession->faces->sortBy('round_number') as $face)
                        <div class="col-md-4 col-sm-6 mb-3">
                            <div class="card">
                                <img src="{{ $face->image_url }}" class="card-img-top" alt="Face Round {{ $face->round_number }}" style="height: 350px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">Round {{ $face->round_number }}</small>
                                        @if($face->detected_emotion)
                                            <span class="badge bg-info">{{ ucfirst($face->detected_emotion) }}</span>
                                        @endif
                                    </div>
                                    <small class="text-muted d-block">{{ $face->captured_at->format('H:i:s') }}</small>
                                    <a href="{{ route('games.show-face', $face->id) }}" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="fas fa-eye me-1"></i>View
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No faces captured</h5>
                        <p class="text-muted">Face captures will appear here when the game captures images.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>


<!-- Timeline View Toggle -->
<div class="mb-4">
    <div class="btn-group" role="group" aria-label="Timeline View Toggle">
        <button type="button" class="btn btn-outline-primary active" id="timeline-horizontal-btn">Horizontal Timeline</button>
        <button type="button" class="btn btn-outline-secondary" id="timeline-vertical-btn">Vertical Timeline</button>
    </div>
</div>

<!-- Timeline Container -->
<div id="timeline-horizontal" class="timeline-view">
    <div class="d-flex flex-row overflow-auto align-items-end" style="gap: 2rem; min-height: 220px;">
        @foreach($gameSession->faces->sortBy('captured_at') as $face)
        <div class="text-center">
            <img src="{{ $face->image_url }}" class="rounded shadow" alt="Face Round {{ $face->round_number }}" style="height: 200px; width: 200px; object-fit: cover; border: 3px solid #007bff;">
            @if($face->screen_image_url)
                <div class="mt-2">
                    <img src="{{ $face->screen_image_url }}" class="rounded border" alt="Gameplay/Screen Image" style="height: 140px; width: 210px; object-fit: cover; border: 2px solid #6c757d;">
                </div>
            @endif
            <div class="mt-2 small text-muted">{{ $face->captured_at->format('H:i:s') }}</div>
            <div class="fw-bold">Round {{ $face->round_number }}</div>
        </div>
        @endforeach
    </div>
</div>

<div id="timeline-vertical" class="timeline-view d-none">
    <div class="d-flex flex-column align-items-start" style="gap: 2rem;">
        @foreach($gameSession->faces->sortBy('captured_at') as $face)
        <div class="d-flex align-items-center">
            <img src="{{ $face->image_url }}" class="rounded shadow me-3" alt="Face Round {{ $face->round_number }}" style="height: 140px; width: 140px; object-fit: cover; border: 3px solid #6c757d;">
            @if($face->screen_image_url)
                <img src="{{ $face->screen_image_url }}" class="rounded border me-3" alt="Gameplay/Screen Image" style="height: 100px; width: 150px; object-fit: cover; border: 2px solid #007bff;">
            @endif
            <div>
                <div class="small text-muted">{{ $face->captured_at->format('M d, H:i:s') }}</div>
                <div class="fw-bold">Round {{ $face->round_number }}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const horizontalBtn = document.getElementById('timeline-horizontal-btn');
    const verticalBtn = document.getElementById('timeline-vertical-btn');
    const horizontalView = document.getElementById('timeline-horizontal');
    const verticalView = document.getElementById('timeline-vertical');

    if (horizontalBtn && verticalBtn && horizontalView && verticalView) {
        horizontalBtn.addEventListener('click', function() {
            horizontalBtn.classList.add('active');
            verticalBtn.classList.remove('active');
            horizontalView.classList.remove('d-none');
            verticalView.classList.add('d-none');
        });
        verticalBtn.addEventListener('click', function() {
            verticalBtn.classList.add('active');
            horizontalBtn.classList.remove('active');
            verticalView.classList.remove('d-none');
            horizontalView.classList.add('d-none');
        });
    }
});
</script>
@endpush
@endsection 