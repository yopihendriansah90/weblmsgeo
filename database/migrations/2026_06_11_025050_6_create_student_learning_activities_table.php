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
        if (Schema::hasTable('student_learning_activities')) {
            return;
        }

        Schema::create('student_learning_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('activity_type');
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_learning_activities');
    }
};
