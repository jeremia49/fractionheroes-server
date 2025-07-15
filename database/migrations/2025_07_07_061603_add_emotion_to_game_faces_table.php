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
        Schema::table('game_faces', function (Blueprint $table) {
            $table->string('detected_emotion')->nullable()->after('face_metadata');
            $table->decimal('emotion_confidence', 5, 2)->nullable()->after('detected_emotion');
            $table->json('emotion_scores')->nullable()->after('emotion_confidence');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_faces', function (Blueprint $table) {
            $table->dropColumn(['detected_emotion', 'emotion_confidence', 'emotion_scores']);
        });
    }
};
