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
        Schema::table('schedule_requests', function (Blueprint $table) {
            $table->string('type')->change();
            $table->date('proposed_date')->nullable()->change();
            $table->time('proposed_start_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedule_requests', function (Blueprint $table) {
            $table->date('proposed_date')->nullable(false)->change();
            $table->time('proposed_start_time')->nullable(false)->change();
        });
    }
};
