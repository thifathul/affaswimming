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
        Schema::create('coach_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->foreignId('coach_availability_id')->nullable()->constrained('coach_availabilities')->onDelete('cascade');
        });
        
        // Delete all 'available' schedules
        \Illuminate\Support\Facades\DB::table('schedules')->where('status', 'available')->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['coach_availability_id']);
            $table->dropColumn('coach_availability_id');
        });
        Schema::dropIfExists('coach_availabilities');
    }
};
