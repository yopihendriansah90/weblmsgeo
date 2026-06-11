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
        Schema::create('quiz_step_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_step_id')->constrained()->cascadeOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->enum('status', ['locked', 'active', 'submitted', 'auto_graded', 'pending_review', 'completed'])->default('locked')->index();
            $table->decimal('score', 6, 2)->nullable();
            $table->json('result_payload')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();

            $table->unique(['quiz_attempt_id', 'quiz_step_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_step_attempts');
    }
};
