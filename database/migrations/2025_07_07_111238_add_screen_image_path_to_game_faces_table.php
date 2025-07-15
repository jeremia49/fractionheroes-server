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
            $table->string('screen_image_path')->nullable()->after('image_filename');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_faces', function (Blueprint $table) {
            $table->dropColumn('screen_image_path');
        });
    }
};
