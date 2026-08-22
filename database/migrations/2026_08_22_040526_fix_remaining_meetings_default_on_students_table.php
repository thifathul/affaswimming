<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing NULL values to 0
        DB::table('students')
            ->whereNull('remaining_meetings')
            ->update(['remaining_meetings' => 0]);

        // Change the column to have a default value of 0
        Schema::table('students', function (Blueprint $table) {
            $table->integer('remaining_meetings')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->integer('remaining_meetings')->nullable()->default(null)->change();
        });
    }
};
