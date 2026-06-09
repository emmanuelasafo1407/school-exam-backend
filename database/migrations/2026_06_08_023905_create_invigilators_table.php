<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invigilators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('staff_id')->unique();
            $table->boolean('is_verified')->default(false); // Gatekeeper flag
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invigilators');
    }
};