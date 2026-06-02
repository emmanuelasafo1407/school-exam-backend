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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            // Connects the profile directly to a user record. If the user is deleted, their profile is deleted automatically.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('student_id_number')->unique();
            $table->string('faculty');
            $table->string('department');
            $table->string('program');
            $table->string('level');
            
            // Stores the directory location path of the uploaded student photo
            $table->string('passport_picture')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};