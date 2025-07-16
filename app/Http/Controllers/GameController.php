<?php

namespace App\Http\Controllers;

use App\Models\GameSession;
use App\Models\GameFace;
use App\Models\Student;
use Illuminate\Http\Request;

class GameController extends Controller
{
    /**
     * Display a listing of game sessions
     */
    public function index()
    {
        $gameSessions = GameSession::with(['student', 'faces'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_sessions' => GameSession::count(),
            'active_sessions' => GameSession::where('game_status', 'active')->count(),
            'total_faces' => GameFace::count(),
            'average_score' => GameSession::where('game_status', 'completed')->avg('total_score') ?? 0,
        ];

        // Get level statistics
        $levelStats = $this->getLevelStatistics();

        return view('games.index', compact('gameSessions', 'stats', 'levelStats'));
    }

    /**
     * Get level statistics for all students
     */
    private function getLevelStatistics()
    {
        $levels = [1, 2, 3];
        $stats = [];

        foreach ($levels as $level) {
            $levelSessions = GameSession::where('level', $level);
            
            $stats[$level] = [
                'sessions_played' => $levelSessions->count(),
                'highest_score' => $levelSessions->max('total_score') ?? 0,
                'average_score' => $levelSessions->avg('total_score') ?? 0,
                'completed_sessions' => $levelSessions->count(),
            ];
        }

        return $stats;
    }

    /**
     * Display the specified game session
     */
    public function show(string $id)
    {
        $gameSession = GameSession::with(['student', 'faces'])
            ->findOrFail($id);

        return view('games.show', compact('gameSession'));
    }

    /**
     * Display game sessions for a specific student
     */
    public function studentSessions(string $studentId)
    {
        $student = Student::with(['gameSessions.faces'])
            ->findOrFail($studentId);

        $gameSessions = $student->gameSessions()
            ->with(['faces'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_sessions' => $student->gameSessions()->count(),
            'completed_sessions' => $student->gameSessions()->where('game_status', 'completed')->count(),
            'total_score' => $student->gameSessions()->sum('total_score'),
            'average_score' => $student->gameSessions()->where('game_status', 'completed')->avg('total_score') ?? 0,
        ];

        // Get level statistics for this student
        $levelStats = $this->getStudentLevelStatistics($studentId);

        return view('games.student-sessions', compact('student', 'gameSessions', 'stats', 'levelStats'));
    }

    /**
     * Get level statistics for a specific student
     */
    private function getStudentLevelStatistics($studentId)
    {
        $levels = [1, 2, 3];
        $stats = [];

        $highestScores = [];
        foreach ($levels as $level) {
            $levelSessions = GameSession::where('student_id', $studentId)->where('level', $level);
            $highestScores[$level] = $levelSessions->max('total_score') ?? 0;
        }
        foreach ($levels as $level) {
            $levelSessions = GameSession::where('student_id', $studentId)->where('level', $level);
            $completedSessions = $levelSessions->where('game_status', 'completed');
            // Find the session with the highest score
            $highestScore = $levelSessions->max('total_score') ?? 0;
            $sessionWithHighestScore = $levelSessions->where('total_score', $highestScore)->orderByDesc('ended_at')->first();
            $duration = $sessionWithHighestScore ? $sessionWithHighestScore->duration : null;
            $stats[$level] = [
                'sessions_played' => $levelSessions->count(),
                'highest_score' => $highestScore,
                'average_score' => $completedSessions->avg('total_score') ?? 0,
                'completed_sessions' => $completedSessions->count(),
                'unlocked' => $level == 1 || $highestScores[$level - 1] >= 70,
                'duration' => $duration,
            ];
        }

        return $stats;
    }

    /**
     * Display all captured faces
     */
    public function faces()
    {
        $faces = GameFace::with(['student', 'gameSession'])
            ->latest()
            ->paginate(20);

        return view('games.faces', compact('faces'));
    }

    /**
     * Display face details
     */
    public function showFace(string $id)
    {
        $face = GameFace::with(['student', 'gameSession'])
            ->findOrFail($id);

        return view('games.show-face', compact('face'));
    }

    /**
     * Update admin notes for a game session
     */
    public function updateNotes(Request $request, string $id)
    {
        $gameSession = GameSession::findOrFail($id);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $gameSession->update([
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Notes updated successfully.');
    }

    /**
     * Update admin notes for a face capture
     */
    public function updateFaceNotes(Request $request, string $id)
    {
        $face = GameFace::findOrFail($id);
        
        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $face->update([
            'notes' => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Face notes updated successfully.');
    }
}
