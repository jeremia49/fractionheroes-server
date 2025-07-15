@extends('layouts.app')

@section('title', 'Face Gallery - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Face Gallery</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('games.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Sessions
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
                            Total Faces</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $faces->total() }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-camera fa-2x text-gray-300"></i>
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
                            Good Quality</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $faces->getCollection()->where('capture_quality', 'good')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
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
                            Poor Quality</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $faces->getCollection()->where('capture_quality', 'poor')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Failed Captures</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $faces->getCollection()->where('capture_quality', 'failed')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face Gallery -->
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Captured Faces</h6>
    </div>
    <div class="card-body">
        @if($faces->count() > 0)
            <div class="row">
                @foreach($faces as $face)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card h-100">
                        <div class="position-relative">
                            <img src="{{ $face->image_url }}" class="card-img-top" alt="Face Capture" style="height: 250px; object-fit: cover;">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-{{ $face->capture_quality === 'good' ? 'success' : ($face->capture_quality === 'poor' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($face->capture_quality) }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h6 class="card-title mb-1">
                                <a href="{{ route('students.show', $face->student->id) }}" class="text-decoration-none">
                                    {{ $face->student->full_name }}
                                </a>
                            </h6>
                            <p class="card-text">
                                <small class="text-muted">
                                    <strong>Session:</strong> {{ $face->gameSession->session_id }}<br>
                                    <strong>Round:</strong> {{ $face->round_number }}<br>
                                    <strong>Emotion:</strong> 
                                    @if($face->detected_emotion)
                                        <span class="badge bg-info">{{ ucfirst($face->detected_emotion) }}</span>
                                        @if($face->emotion_confidence)
                                            <small>({{ number_format($face->emotion_confidence, 1) }}%)</small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                    <br>
                                    <strong>Captured:</strong> {{ $face->captured_at->format('M d, Y H:i:s') }}
                                </small>
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('games.show-face', $face->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>View Details
                                </a>
                                <a href="{{ route('games.show', $face->gameSession->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-gamepad me-1"></i>Session
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="d-flex justify-content-center">
                {{ $faces->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No faces captured</h5>
                <p class="text-muted">Face captures will appear here when students play the game.</p>
            </div>
        @endif
    </div>
</div>
@endsection 