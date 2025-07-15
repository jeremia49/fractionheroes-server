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
        Schema::table('students', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'student_id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'date_of_birth',
                'gender',
                'address',
                'section',
                'enrollment_date',
                'notes',
                'status'
            ]);
            // Add new columns
            $table->string('username')->unique()->after('id');
            $table->string('password');
            $table->string('full_name');
            $table->string('school')->nullable();
            $table->string('class')->nullable()->change(); // already exists, just change
            $table->string('class_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Add old columns back
            $table->string('student_id')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('date_of_birth');
            $table->string('gender');
            $table->text('address')->nullable();
            $table->string('section')->nullable();
            $table->date('enrollment_date');
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            // Drop new columns
            $table->dropColumn([
                'username',
                'password',
                'full_name',
                'school',
                'class_type'
            ]);
        });
    }
};
