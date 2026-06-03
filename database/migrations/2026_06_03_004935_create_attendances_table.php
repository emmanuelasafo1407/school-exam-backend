<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('student_id_number');
            $table->string('course_code');
            $table->string('course_name');
            $table->string('hall');
            $table->unsignedBigInteger('invigilator_id'); // Tracks which supervisor clocked them in
            $table->timestamp('verified_at'); // Exact timestamp of check-in
            $table->timestamps();

            // Relational foreign key binding to our main users table
            $table->foreign('invigilator_id')->references('id')->on('users')->onDelete('cascade');
            
            // Prevent marking a student present multiple times for the exact same course exam
            $table->unique(['student_id_number', 'course_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};