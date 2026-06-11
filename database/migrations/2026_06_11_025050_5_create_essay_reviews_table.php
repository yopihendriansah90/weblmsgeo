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
        Schema::create('essay_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_step_attempt_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('score', 6, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['pending_review', 'reviewed'])->default('pending_review')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essay_reviews');
    }
};
