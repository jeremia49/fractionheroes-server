<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\Student; // Added this import for the new studentLogin method

class AuthController extends Controller
{


    /**
     * Register new student
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|min:5|max:15|unique:students,username',
            'password' => 'required|string|min:8',
            'full_name' => 'required|string|max:255',
            'school' => 'nullable|string|max:255',
            'class' => 'nullable|string|max:50',
            'class_type' => 'nullable|string|max:50',
        ]);

        $student = Student::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'school' => $request->school,
            'class' => $request->class,
            'class_type' => $request->class_type,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Student registered successfully',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'username' => $student->username,
                    'full_name' => $student->full_name,
                    'school' => $student->school,
                    'class' => $student->class,
                    'class_type' => $student->class_type,
                ],
            ]
        ], 201);
    }

    /**
     * Logout user and revoke token
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    /**
     * Student login using username and password
     */
    public function studentLogin(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $student = Student::where('username', $request->username)->first();

        if (!$student || !Hash::check($request->password, $student->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Create a simple token for student (you might want to use Sanctum tokens here)
        $token = base64_encode($student->id . '|' . time());

        return response()->json([
            'success' => true,
            'message' => 'Student login successful',
            'data' => [
                'student' => [
                    'id' => $student->id,
                    'username' => $student->username,
                    'full_name' => $student->full_name,
                    'school' => $student->school,
                    'class' => $student->class,
                    'class_type' => $student->class_type,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Get the authenticated user's info
     */
    public function user(Request $request)
    {
        $authHeader = $request->header('Authorization');
        if ($authHeader && preg_match('/Bearer\\s(.+)/', $authHeader, $matches)) {
            $token = $matches[1];
            $parts = explode('|', base64_decode($token));
            if (count($parts) >= 1) {
                $studentId = $parts[0];
                $student = Student::find($studentId);
                
                if ($student) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Authenticated student info',
                        'data' => [
                            'student' => [
                                'id' => $student->id,
                                'username' => $student->username,
                                'full_name' => $student->full_name,
                                'school' => $student->school,
                                'class' => $student->class,
                                'class_type' => $student->class_type,
                            ]
                        ]
                    ], 200);
                }
            }
        }

        // If not authenticated
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated'
        ], 401);
    }
} 