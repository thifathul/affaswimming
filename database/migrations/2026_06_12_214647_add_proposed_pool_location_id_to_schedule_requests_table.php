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
            $table->foreignId('proposed_pool_location_id')->nullable()->after('substitute_coach_id')->constrained('pool_locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_requests', function (Blueprint $table) {
            $table->dropForeign(['proposed_pool_location_id']);
            $table->dropColumn('proposed_pool_location_id');
        });
    }
};
