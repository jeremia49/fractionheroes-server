<?php

namespace Tests\Feature;

use App\Models\GameSession;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GameSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_game_session_automatically_marked_completed_when_score_70_or_higher()
    {
        // Create a student
        $student = Student::factory()->create();
        
        // Create a game session
        $gameSession = GameSession::create([
            'student_id' => $student->id,
            'session_id' => 'GAME_TEST_001',
            'started_at' => now(),
            'total_score' => 0,
            'game_status' => 'active',
            'level' => 1,
        ]);

        // End session with score >= 70 (should be marked as completed)
        $response = $this->postJson('/api/v1/games/end-session', [
            'game_session_id' => $gameSession->id,
            'final_score' => 85,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'game_status' => 'completed',
                'final_score' => 85,
            ]
        ]);

        // Verify the session was updated in the database
        $gameSession->refresh();
        $this->assertEquals('completed', $gameSession->game_status);
        $this->assertEquals(85, $gameSession->total_score);
    }

    public function test_game_session_automatically_marked_uncompleted_when_score_below_70()
    {
        // Create a student
        $student = Student::factory()->create();
        
        // Create a game session
        $gameSession = GameSession::create([
            'student_id' => $student->id,
            'session_id' => 'GAME_TEST_002',
            'started_at' => now(),
            'total_score' => 0,
            'game_status' => 'active',
            'level' => 1,
        ]);

        // End session with score < 70 (should be marked as uncompleted)
        $response = $this->postJson('/api/v1/games/end-session', [
            'game_session_id' => $gameSession->id,
            'final_score' => 65,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'game_status' => 'uncompleted',
                'final_score' => 65,
            ]
        ]);

        // Verify the session was updated in the database
        $gameSession->refresh();
        $this->assertEquals('uncompleted', $gameSession->game_status);
        $this->assertEquals(65, $gameSession->total_score);
    }

    public function test_game_session_respects_explicit_status_when_provided()
    {
        // Create a student
        $student = Student::factory()->create();
        
        // Create a game session
        $gameSession = GameSession::create([
            'student_id' => $student->id,
            'session_id' => 'GAME_TEST_003',
            'started_at' => now(),
            'total_score' => 0,
            'game_status' => 'active',
            'level' => 1,
        ]);

        // End session with explicit status (should override automatic logic)
        $response = $this->postJson('/api/v1/games/end-session', [
            'game_session_id' => $gameSession->id,
            'final_score' => 90,
            'game_status' => 'uncompleted', // Explicitly set to uncompleted despite high score
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'game_status' => 'uncompleted',
                'final_score' => 90,
            ]
        ]);

        // Verify the session was updated in the database
        $gameSession->refresh();
        $this->assertEquals('uncompleted', $gameSession->game_status);
        $this->assertEquals(90, $gameSession->total_score);
    }

    public function test_game_session_with_score_exactly_70_is_marked_completed()
    {
        // Create a student
        $student = Student::factory()->create();
        
        // Create a game session
        $gameSession = GameSession::create([
            'student_id' => $student->id,
            'session_id' => 'GAME_TEST_004',
            'started_at' => now(),
            'total_score' => 0,
            'game_status' => 'active',
            'level' => 1,
        ]);

        // End session with score exactly 70 (should be marked as completed)
        $response = $this->postJson('/api/v1/games/end-session', [
            'game_session_id' => $gameSession->id,
            'final_score' => 70,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'game_status' => 'completed',
                'final_score' => 70,
            ]
        ]);

        // Verify the session was updated in the database
        $gameSession->refresh();
        $this->assertEquals('completed', $gameSession->game_status);
        $this->assertEquals(70, $gameSession->total_score);
    }
} 