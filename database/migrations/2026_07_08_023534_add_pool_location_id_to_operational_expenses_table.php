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
        Schema::table('operational_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('pool_location_id')->nullable()->after('id');
            $table->foreign('pool_location_id')->references('id')->on('pool_locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_expenses', function (Blueprint $table) {
            $table->dropForeign(['pool_location_id']);
            $table->dropColumn('pool_location_id');
        });
    }
};
