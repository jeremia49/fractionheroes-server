@extends('layouts.app')

@section('title', 'Face Capture Details - Student Dashboard')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Face Capture Details</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('games.faces') }}" class="btn btn-secondary me-2">
            <i class="fas fa-arrow-left me-1"></i>Back to Gallery
        </a>
        <a href="{{ route('games.show', $face->gameSession->id) }}" class="btn btn-info">
            <i class="fas fa-gamepad me-1"></i>View Session
        </a>
    </div>
</div>

<div class="row">
    <!-- Face Image -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Captured Face</h6>
            </div>
            <div class="card-body text-center">
                <img src="{{ $face->image_url }}" class="img-fluid rounded" alt="Face Capture" style="max-height: 500px;">
                @if($face->screen_image_url)
                    <hr>
                    <h6 class="mt-3">Gameplay/Screen Image</h6>
                    <img src="{{ $face->screen_image_url }}" class="img-fluid rounded border" alt="Gameplay/Screen Image" style="max-height: 500px;">
                @endif
            </div>
        </div>
    </div>
    
    <!-- Face Information -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Capture Information</h6>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-camera fa-lg text-white"></i>
                    </div>
                    <h6 class="mt-2 mb-1">Round {{ $face->round_number }}</h6>
                    <p class="text-muted mb-0">{{ $face->student->full_name }}</p>
                </div>
                
                <hr>
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Quality:</strong></div>
                    <div class="col-7">
                        <span class="badge bg-{{ $face->capture_quality === 'good' ? 'success' : ($face->capture_quality === 'poor' ? 'warning' : 'danger') }}">
                            {{ ucfirst($face->capture_quality) }}
                        </span>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Student:</strong></div>
                    <div class="col-7">
                        <a href="{{ route('students.show', $face->student->id) }}" class="text-decoration-none">
                            {{ $face->student->full_name }}
                        </a>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Session:</strong></div>
                    <div class="col-7">
                        <a href="{{ route('games.show', $face->gameSession->id) }}" class="text-decoration-none">
                            {{ $face->gameSession->session_id }}
                        </a>
                    </div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Round:</strong></div>
                    <div class="col-7">{{ $face->round_number }}</div>
                </div>
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Captured:</strong></div>
                    <div class="col-7">{{ $face->captured_at->format('M d, Y H:i:s') }}</div>
                </div>
                
                @if($face->confidence_score)
                <div class="row mb-2">
                    <div class="col-5"><strong>Confidence:</strong></div>
                    <div class="col-7">
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar" style="width: {{ $face->confidence_score }}%">
                                {{ number_format($face->confidence_score, 1) }}%
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                @if($face->face_landmarks)
                <div class="row mb-2">
                    <div class="col-5"><strong>Landmarks:</strong></div>
                    <div class="col-7">
                        <small class="text-muted">{{ $face->face_landmarks }}</small>
                    </div>
                </div>
                @endif
                
                @if($face->capture_notes)
                <div class="row mb-2">
                    <div class="col-5"><strong>Notes:</strong></div>
                    <div class="col-7">{{ $face->capture_notes }}</div>
                </div>
                @endif
                
                <div class="row mb-2">
                    <div class="col-5"><strong>Detected Emotion:</strong></div>
                    <div class="col-7">
                        @if($face->detected_emotion)
                            <span class="badge bg-info">{{ ucfirst($face->detected_emotion) }}</span>
                            @if($face->emotion_confidence)
                                <small class="text-muted ms-2">({{ number_format($face->emotion_confidence, 1) }}%)</small>
                            @endif
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
                
                <!-- Update Notes Form -->
                <hr>
                <form method="POST" action="{{ route('games.update-face-notes', $face->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-3">
                        <label for="capture_notes" class="form-label">Admin Notes</label>
                        <textarea class="form-control" id="capture_notes" name="capture_notes" rows="3" placeholder="Add notes about this capture...">{{ $face->capture_notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Update Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Navigation to Other Faces in Session -->
@if($face->gameSession->faces->count() > 1)
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Other Faces in This Session</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($face->gameSession->faces->where('id', '!=', $face->id)->take(4) as $otherFace)
                    <div class="col-md-3 col-sm-6 mb-3">
                        <div class="card">
                            <img src="{{ $otherFace->image_url }}" class="card-img-top" alt="Face Round {{ $otherFace->round_number }}" style="height: 150px; object-fit: cover;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Round {{ $otherFace->round_number }}</small>
                                    <span class="badge bg-{{ $otherFace->capture_quality === 'good' ? 'success' : ($otherFace->capture_quality === 'poor' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($otherFace->capture_quality) }}
                                    </span>
                                </div>
                                <a href="{{ route('games.show-face', $otherFace->id) }}" class="btn btn-sm btn-outline-primary mt-1 w-100">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @if($face->gameSession->faces->count() > 5)
                <div class="text-center mt-3">
                    <a href="{{ route('games.show', $face->gameSession->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-images me-1"></i>View All Faces in Session
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection 