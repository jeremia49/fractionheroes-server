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
        Schema::create('game_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique(); // Unique session identifier
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->integer('total_score')->default(0);
            $table->integer('total_rounds')->default(0);
            $table->integer('completed_rounds')->default(0);
            $table->string('game_status')->default('active'); // active, completed, abandoned
            $table->json('game_settings')->nullable(); // Store game configuration
            $table->text('notes')->nullable(); // Admin notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_sessions');
    }
};
