<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:students,username',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string|max:255',
            'school' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:50',
            'class_type' => 'nullable|string|max:50',
        ]);

        $student = Student::create([
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'full_name' => $request->full_name,
            'school' => $request->school,
            'class' => $request->class,
            'class_type' => $request->class_type,
        ]);

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::with('gameSessions')->findOrFail($id);
        $levelStats = $this->getStudentLevelStatistics($id);
        return view('students.show', compact('student', 'levelStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        $request->validate([
            'username' => 'required|string|max:255|unique:students,username,' . $id,
            'password' => 'nullable|string|min:8',
            'full_name' => 'required|string|max:255',
            'school' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:50',
            'class_type' => 'nullable|string|max:50',
        ]);

        $data = [
            'username' => $request->username,
            'full_name' => $request->full_name,
            'school' => $request->school,
            'class' => $request->class,
            'class_type' => $request->class_type,
        ];
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $student->update($data);

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
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
            $levelSessions = \App\Models\GameSession::where('student_id', $studentId)->where('level', $level);
            $highestScores[$level] = $levelSessions->max('total_score') ?? 0;
        }
        foreach ($levels as $level) {
            $levelSessions = \App\Models\GameSession::where('student_id', $studentId)->where('level', $level);
            // Find the session with the highest score
            $highestScore = $levelSessions->max('total_score') ?? 0;
            $sessionWithHighestScore = $levelSessions->where('total_score', $highestScore)->orderByDesc('ended_at')->first();
            $duration = $sessionWithHighestScore ? $sessionWithHighestScore->duration : null;
            $stats[$level] = [
                'sessions_played' => $levelSessions->count(),
                'highest_score' => $highestScore,
                'average_score' => $levelSessions->avg('total_score') ?? 0,
                'completed_sessions' => $levelSessions->count(),
                'unlocked' => $level == 1 || $highestScores[$level - 1] >= 70,
                'duration' => $duration,
            ];
        }

        return $stats;
    }
}
