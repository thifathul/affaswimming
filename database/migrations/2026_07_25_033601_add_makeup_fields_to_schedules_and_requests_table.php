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
        Schema::table('schedule_requests', function (Blueprint $table) {
            $table->json('absent_student_ids')->nullable()->after('admin_note');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->boolean('is_makeup')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_requests', function (Blueprint $table) {
            $table->dropColumn('absent_student_ids');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('is_makeup');
        });
    }
};
