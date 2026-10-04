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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company', 150);
            $table->string('position', 150);
            $table->string('location', 150);
            $table->string('job_type', 50); // Full-time, Part-time, Internship, Contract
            $table->decimal('salary', 12, 2)->nullable();
            $table->date('applied_date');
            $table->date('follow_up_date')->nullable();
            $table->string('status', 50)->default('Applied'); // Applied, Shortlisted, Interview, Selected, Rejected
            $table->string('job_url', 2048)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for fast lookups & filtering
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'follow_up_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
