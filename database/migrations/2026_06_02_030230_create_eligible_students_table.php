<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eligible_students', function (Blueprint $table) {
            $table->id();
            $table->string('student_id_number')->unique();
            $table->string('full_name');
            $table->string('level');
            $table->string('semester'); // e.g., "Sem 1 2026"
            $table->boolean('has_registered')->default(false); // Keeps track of who completed onboarding
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eligible_students');
    }
};