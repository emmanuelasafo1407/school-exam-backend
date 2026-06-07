<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->string('floor_level')->default('Ground Floor');
            $table->string('map_link')->nullable(); // URL to campus map
            $table->text('seating_info')->nullable(); // e.g., "Rows 1-5, Center"
        });
    }

    public function down(): void
    {
        Schema::table('exam_schedules', function (Blueprint $table) {
            $table->dropColumn(['floor_level', 'map_link', 'seating_info']);
        });
    }
};