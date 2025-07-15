<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    /**
     * Display a listing of students
     */
    public function index()
    {
        $students = Student::latest()->paginate(10);
        
        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully',
            'data' => $students
        ], 200);
    }

    /**
     * Store a newly created student
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

        return response()->json([
            'success' => true,
            'message' => 'Student created successfully',
            'data' => $student
        ], 201);
    }

    /**
     * Display the specified student
     */
    public function show(string $id)
    {
        $student = Student::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'message' => 'Student retrieved successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Update the specified student
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

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully',
            'data' => $student
        ], 200);
    }

    /**
     * Remove the specified student
     */
    public function destroy(string $id)
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully'
        ], 200);
    }
} 