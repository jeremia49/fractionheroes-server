<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameFace extends Model
{
    protected $fillable = [
        'game_session_id',
        'student_id',
        'image_path',
        'image_filename',
        'screen_image_path',
        'round_number',
        'captured_at',
        'face_metadata',
        'capture_quality',
        'notes',
        'detected_emotion',
        'emotion_confidence',
        'emotion_scores',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'face_metadata' => 'array',
        'emotion_scores' => 'array',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->image_path);
    }

    public function getScreenImageUrlAttribute()
    {
        return $this->screen_image_path ? asset('storage/' . $this->screen_image_path) : null;
    }
}
