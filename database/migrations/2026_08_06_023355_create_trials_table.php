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
        Schema::create('trials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('age');
            $table->enum('gender', ['L', 'P']);
            $table->string('school')->nullable();
            $table->string('contact_number')->nullable();
            $table->foreignId('pool_location_id')->nullable()->constrained('pool_locations')->nullOnDelete();
            $table->foreignId('coach_id')->constrained('users')->cascadeOnDelete();
            $table->date('schedule_date');
            $table->time('schedule_time')->nullable();
            $table->enum('status', ['pending', 'hadir', 'absen'])->default('pending');
            $table->text('report_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trials');
    }
};
