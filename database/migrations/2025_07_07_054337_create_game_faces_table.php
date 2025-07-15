<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_faces', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_session_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('image_path'); // Path to stored face image
            $table->string('image_filename'); // Original filename
            $table->integer('round_number'); // Which round this face was captured
            $table->timestamp('captured_at'); // When the face was captured
            $table->json('face_metadata')->nullable(); // Face detection data, emotions, etc.
            $table->string('capture_quality')->default('good'); // good, poor, failed
            $table->text('notes')->nullable(); // Admin notes about the capture
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_faces');
    }
};
