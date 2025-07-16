<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GameSession;
use App\Models\GameFace;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class GameController extends Controller
{
    /**
     * Start a new game session
     */
    public function startSession(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'game_settings' => 'nullable|array',
            'level' => 'required|integer|min:1|max:3',
        ]);

        $session = GameSession::create([
            'student_id' => $request->student_id,
            'session_id' => 'GAME_' . Str::random(12),
            'started_at' => now(),
            'game_settings' => $request->game_settings,
            'game_status' => 'active',
            'level' => $request->level,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game session started successfully',
            'data' => [
                'session_id' => $session->session_id,
                'game_session_id' => $session->id,
                'started_at' => $session->started_at,
            ]
        ], 201);
    }

    /**
     * Capture and store a face image
     */
    public function captureFace(Request $request)
    {
        $request->validate([
            'game_session_id' => 'required|exists:game_sessions,id',
            'student_id' => 'required|exists:students,id',
            'round_number' => 'required|integer|min:1',
            'face_image' => 'required|image|max:5120', // 5MB max
            'screen_image' => 'nullable|image|max:5120', // 5MB max, optional
            'face_metadata' => 'nullable|array',
            'capture_quality' => 'nullable|in:good,poor,failed',
            'detected_emotion' => 'nullable|string|max:50',
            'emotion_confidence' => 'nullable|numeric|min:0|max:100',
            'emotion_scores' => 'nullable|array',
        ]);

        $session = GameSession::findOrFail($request->game_session_id);
        
        // Store the face image
        $imagePath = $request->file('face_image')->store('game-faces', 'public');
        $filename = $request->file('face_image')->getClientOriginalName();

        // Store the screen image if present
        $screenImagePath = null;
        if ($request->hasFile('screen_image')) {
            $screenImagePath = $request->file('screen_image')->store('game-screens', 'public');
        }

        $face = GameFace::create([
            'game_session_id' => $request->game_session_id,
            'student_id' => $request->student_id,
            'image_path' => $imagePath,
            'image_filename' => $filename,
            'screen_image_path' => $screenImagePath,
            'round_number' => $request->round_number,
            'captured_at' => now(),
            'face_metadata' => $request->face_metadata,
            'capture_quality' => $request->capture_quality ?? 'good',
            'detected_emotion' => $request->detected_emotion,
            'emotion_confidence' => $request->emotion_confidence,
            'emotion_scores' => $request->emotion_scores,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Face captured successfully',
            'data' => [
                'face_id' => $face->id,
                'image_url' => $face->image_url,
                'screen_image_path' => $face->screen_image_path,
                'captured_at' => $face->captured_at,
            ]
        ], 201);
    }

    /**
     * End a game session
     */
    public function endSession(Request $request)
    {
        $request->validate([
            'game_session_id' => 'required|exists:game_sessions,id',
            'final_score' => 'nullable|integer|min:0',
            // 'game_status' => 'nullable|in:completed,uncompleted', // now optional
        ]);

        $session = GameSession::findOrFail($request->game_session_id);
        
        // Determine the final score
        $finalScore = $request->final_score ?? $session->total_score;
        
        $gameStatus = $finalScore >= 70 ? 'completed' : 'uncompleted';
        
        $session->update([
            'ended_at' => now(),
            'game_status' => $gameStatus,
            'total_score' => $finalScore,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Game session ended successfully',
            'data' => [
                'session_id' => $session->session_id,
                'final_score' => $session->total_score,
                'duration' => $session->duration,
                'average_score' => $session->average_score,
                'game_status' => $session->game_status,
            ]
        ], 200);
    }

    /**
     * Get game session details
     */
    public function getSession(string $sessionId)
    {
        $session = GameSession::with(['student', 'faces'])
            ->where('session_id', $sessionId)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'message' => 'Session details retrieved successfully',
            'data' => $session
        ], 200);
    }

    /**
     * Get student's game history
     */
    public function getStudentHistory(string $studentId)
    {
        $student = Student::with(['gameSessions.faces'])
            ->findOrFail($studentId);

        return response()->json([
            'success' => true,
            'message' => 'Student game history retrieved successfully',
            'data' => [
                'student' => $student,
                'total_sessions' => $student->gameSessions->count(),
                'total_faces' => $student->gameFaces->count(),
                'average_score' => $student->gameSessions->avg('total_score') ?? 0,
            ]
        ], 200);
    }

    /**
     * Get student's level unlock status and session count for each level
     */
    public function getLevelStatus(string $studentId)
    {
        $levels = [1, 2, 3];
        $result = [];
        $sessions = GameSession::where('student_id', $studentId)->get();
        $highestScores = [];
        foreach ($levels as $level) {
            $levelHighest = $sessions->where('level', $level)->max('total_score') ?? 0;
            $highestScores[$level] = $levelHighest;
        }
        foreach ($levels as $level) {
            if ($level === 1) {
                $unlocked = true;
            } else {
                $unlocked = $highestScores[$level - 1] >= 70;
            }
            $levelSessions = $sessions->where('level', $level);
            $highestScore = $levelSessions->max('total_score') ?? 0;
            $sessionWithHighestScore = $levelSessions->where('total_score', $highestScore)->sortByDesc('ended_at')->first();
            $duration = $sessionWithHighestScore ? $sessionWithHighestScore->duration : null;
            $durationFormatted = $duration !== null ? gmdate('H:i:s', $duration) : null;
            $result[] = [
                'level' => $level,
                'unlocked' => $unlocked,
                'sessions_played' => $levelSessions->count(),
                'highest_score' => $highestScore,
                'duration' => $durationFormatted,
            ];
        }
        return response()->json([
            'success' => true,
            'levels' => $result,
        ]);
    }

    /**
     * Get global leaderboard (top students by total score)
     */
    public function leaderboard(Request $request)
    {
        $limit = $request->input('limit', 10);
        $students = \App\Models\Student::with('gameSessions')
            ->get()
            ->map(function ($student) {
                $totalScore = $student->gameSessions->sum('total_score');
                $averageScore = $student->gameSessions->avg('total_score') ?: 0;
                return [
                    'id' => $student->id,
                    'username' => $student->username,
                    'full_name' => $student->full_name,
                    'school' => $student->school,
                    'class' => $student->class,
                    'class_type' => $student->class_type,
                    'total_score' => $totalScore,
                    'average_score' => round($averageScore, 2),
                    'sessions_played' => $student->gameSessions->count(),
                ];
            })
            ->sortByDesc('total_score')
            ->values()
            ->take($limit);

        return response()->json([
            'success' => true,
            'leaderboard' => $students,
        ]);
    }
}
