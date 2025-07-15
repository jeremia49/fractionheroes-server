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
        Schema::dropIfExists('progress');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('subject');
            $table->decimal('overall_grade', 5, 2);
            $table->text('comments')->nullable();
            $table->date('assessment_date');
            $table->timestamps();
        });
    }
};
