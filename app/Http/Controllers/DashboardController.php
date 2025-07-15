<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\GameSession;
use App\Models\GameFace;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        
        // Game statistics
        $totalGameSessions = GameSession::count();
        $activeGameSessions = GameSession::where('game_status', 'active')->count();
        $totalFaces = GameFace::count();
        $averageGameScore = GameSession::where('game_status', 'completed')->avg('total_score') ?? 0;
        
        $recentStudents = Student::latest()->take(5)->get();
        $recentGameSessions = GameSession::with('student')->latest()->take(5)->get();
        
        return view('dashboard', compact(
            'totalStudents',
            'totalGameSessions',
            'activeGameSessions',
            'totalFaces',
            'averageGameScore',
            'recentStudents',
            'recentGameSessions'
        ));
    }
}
