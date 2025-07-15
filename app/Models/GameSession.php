<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    protected $fillable = [
        'student_id',
        'session_id',
        'level',
        'started_at',
        'ended_at',
        'total_score',
        'total_rounds',
        'completed_rounds',
        'game_status',
        'game_settings',
        'notes'
    ];

    protected $casts = [
        'level' => 'integer',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'game_settings' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function faces(): HasMany
    {
        return $this->hasMany(GameFace::class);
    }

    public function getDurationAttribute()
    {
        if ($this->ended_at) {
            return $this->started_at->diffInSeconds($this->ended_at);
        }
        return null;
    }

    public function getAverageScoreAttribute()
    {
        if ($this->completed_rounds > 0) {
            return round($this->total_score / $this->completed_rounds, 2);
        }
        return 0;
    }
}
