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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_qualified')->default(false); // For students (fees paid)
            $table->boolean('is_verified')->default(false);  // For invigilators (account approved)
            $table->string('assigned_hall')->nullable();     // For invigilators
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_qualified', 'is_verified', 'assigned_hall']);
        });
    }
};