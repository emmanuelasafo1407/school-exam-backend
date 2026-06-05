<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop fields if they were partially added, then enforce the clean schema
            $table->dateTime('start_time')->nullable()->change();
            $table->dateTime('end_time')->nullable()->change();
            
            // Add the missing Lecturer column
            if (!Schema::hasColumn('attendances', 'lecturer_name')) {
                $table->string('lecturer_name')->nullable()->after('course_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['lecturer_name']);
        });
    }
};