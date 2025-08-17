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

<!-- Top Row: Session Info and Emotion Chart -->
<div class="row mb-4">
    <!-- Session Information -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Session Information</h6>
                <span class="badge bg-{{ $gameSession->game_status === 'completed' ? 'success' : ($gameSession->game_status === 'uncompleted' ? 'danger' : 'primary') }}">
                    {{ ucfirst($gameSession->game_status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-2">{{ $gameSession->session_id }}</h5>
                        <p class="text-muted mb-3">{{ $gameSession->student->full_name }}</p>
                        
                        <div class="mb-2">
                            <strong>Level:</strong>
                            <span class="badge bg-{{ $gameSession->level == 1 ? 'primary' : ($gameSession->level == 2 ? 'success' : 'warning') }}">
                                Level {{ $gameSession->level }}
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <strong>Score:</strong> {{ $gameSession->total_score }}
                            @if($gameSession->completed_rounds > 0)
                                <small class="text-muted">({{ number_format($gameSession->average_score, 1) }} avg)</small>
                            @endif
                        </div>
                        
                        <div class="mb-2">
                            <strong>Progress:</strong> {{ $gameSession->completed_rounds }} rounds
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-2">
                            <strong>Started:</strong><br>
                            <small>{{ $gameSession->started_at->format('M d, Y H:i') }}</small>
                        </div>
                        
                        @if($gameSession->ended_at)
                        <div class="mb-2">
                            <strong>Duration:</strong><br>
                            <small>{{ gmdate('H:i:s', $gameSession->duration) }}</small>
                        </div>
                        @endif
                        
                        @if($gameSession->notes)
                        <div class="mb-2">
                            <strong>Notes:</strong><br>
                            <small>{{ Str::limit($gameSession->notes, 100) }}</small>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Compact Notes Form -->
                <hr>
                <form method="POST" action="{{ route('games.update-notes', $gameSession->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="mb-2">
                        <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2" placeholder="Add notes...">{{ $gameSession->notes }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-save me-1"></i>Update Notes
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Emotion Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Emotion Distribution</h6>
            </div>
            <div class="card-body">
                <canvas id="emotionChart" width="400" height="200"></canvas>
                <div class="mt-2">
                    <small class="text-muted">
                        @foreach($emotionData as $emotion => $count)
                            <span class="badge bg-light text-dark me-1">{{ $emotion }}: {{ $count }}</span>
                        @endforeach
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Captured Faces - Scrollable Section -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Captured Faces ({{ $gameSession->faces->count() }})</h6>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-primary active" id="grid-view-btn">Grid</button>
            <button type="button" class="btn btn-outline-secondary" id="timeline-view-btn">Timeline</button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($gameSession->faces->count() > 0)
            <!-- Grid View -->
            <div id="grid-view" class="p-3">
                <div class="row">
                    @foreach($gameSession->faces->sortBy('round_number') as $face)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                        <div class="card h-100">
                            <img src="{{ $face->image_url }}" class="card-img-top" alt="Face Round {{ $face->round_number }}" style="height: 200px; object-fit: cover;">
                            <div class="card-body p-2">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Round {{ $face->round_number }}</small>
                                    @if($face->detected_emotion)
                                        <span class="badge bg-info">{{ ucfirst($face->detected_emotion) }}</span>
                                    @endif
                                </div>
                                <small class="text-muted d-block mb-2">{{ $face->captured_at->format('H:i:s') }}</small>
                                <a href="{{ route('games.show-face', $face->id) }}" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            
            <!-- Timeline View -->
            <div id="timeline-view" class="d-none" style="max-height: 400px; overflow-y: auto;">
                <div class="p-3">
                    <div class="d-flex flex-row overflow-auto align-items-end" style="gap: 1rem; min-height: 180px;">
                        @foreach($gameSession->faces->sortBy('captured_at') as $face)
                        <div class="text-center flex-shrink-0">
                            <img src="{{ $face->image_url }}" class="rounded shadow" alt="Face Round {{ $face->round_number }}" style="height: 150px; width: 150px; object-fit: cover; border: 2px solid #007bff;">
                            @if($face->screen_image_url)
                                <div class="mt-1">
                                    <img src="{{ $face->screen_image_url }}" class="rounded border" alt="Gameplay" style="height: 100px; width: 140px; object-fit: cover;">
                                </div>
                            @endif
                            <div class="mt-1 small text-muted">{{ $face->captured_at->format('H:i:s') }}</div>
                            <div class="fw-bold small">Round {{ $face->round_number }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
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




@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality for faces
    const gridViewBtn = document.getElementById('grid-view-btn');
    const timelineViewBtn = document.getElementById('timeline-view-btn');
    const gridView = document.getElementById('grid-view');
    const timelineView = document.getElementById('timeline-view');

    if (gridViewBtn && timelineViewBtn && gridView && timelineView) {
        gridViewBtn.addEventListener('click', function() {
            gridViewBtn.classList.add('active');
            timelineViewBtn.classList.remove('active');
            gridView.classList.remove('d-none');
            timelineView.classList.add('d-none');
        });
        timelineViewBtn.addEventListener('click', function() {
            timelineViewBtn.classList.add('active');
            gridViewBtn.classList.remove('active');
            timelineView.classList.remove('d-none');
            gridView.classList.add('d-none');
        });
    }

    // Emotion Chart
    const emotionChartCanvas = document.getElementById('emotionChart');
    if (emotionChartCanvas) {
        const emotionData = @json($emotionData ?? []);
        
        if (emotionData && Object.keys(emotionData).length > 0) {
            const ctx = emotionChartCanvas.getContext('2d');
            
            // Define colors for all 9 emotions
            const emotionColors = {
                'Angry': { bg: 'rgba(220, 53, 69, 0.8)', border: 'rgba(220, 53, 69, 1)' },
                'Disgust': { bg: 'rgba(40, 167, 69, 0.8)', border: 'rgba(40, 167, 69, 1)' },
                'Fear': { bg: 'rgba(255, 193, 7, 0.8)', border: 'rgba(255, 193, 7, 1)' },
                'Happy': { bg: 'rgba(255, 206, 86, 0.8)', border: 'rgba(255, 206, 86, 1)' },
                'Sad': { bg: 'rgba(108, 117, 125, 0.8)', border: 'rgba(108, 117, 125, 1)' },
                'Surprise': { bg: 'rgba(255, 159, 64, 0.8)', border: 'rgba(255, 159, 64, 1)' },
                'Neutral': { bg: 'rgba(199, 199, 199, 0.8)', border: 'rgba(199, 199, 199, 1)' },
                'Contempt': { bg: 'rgba(153, 102, 255, 0.8)', border: 'rgba(153, 102, 255, 1)' },
                'No faces detected': { bg: 'rgba(23, 162, 184, 0.8)', border: 'rgba(23, 162, 184, 1)' }
            };
            
            const labels = Object.keys(emotionData);
            const data = Object.values(emotionData);
            const backgroundColor = labels.map(label => emotionColors[label]?.bg || 'rgba(199, 199, 199, 0.8)');
            const borderColor = labels.map(label => emotionColors[label]?.border || 'rgba(199, 199, 199, 1)');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Number of Detections',
                        data: data,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            ticks: {
                                stepSize: 1,
                                maxTicksLimit: 5
                            },
                            grid: {
                                display: true,
                                drawBorder: false
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' detection(s)';
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 10,
                            bottom: 10
                        }
                    }
                }
            });
        }
    }
});
</script>
@endpush
@endsection 