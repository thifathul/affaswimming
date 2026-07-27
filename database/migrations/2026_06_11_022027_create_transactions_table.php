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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('pool_location_id')->constrained()->onDelete('cascade');
            $table->integer('package_type')->comment('e.g., 4 or 8');
            $table->enum('class_type', ['private', 'semi_private'])->nullable();
            $table->integer('amount');
            $table->date('practice_start_date');
            $table->string('proof_of_payment');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->integer('coach_salary_cut')->nullable();
            $table->integer('pool_ticket_cut')->nullable();
            $table->integer('cash_cut')->nullable();
            $table->integer('profit_cut')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
