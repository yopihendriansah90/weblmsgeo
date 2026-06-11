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
        Schema::create('quiz_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->enum('type', ['essay', 'text_matching', 'table_checklist', 'image_text_matching']);
            $table->text('instruction')->nullable();
            $table->json('content_payload')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->enum('answer_mode', ['single_answer_per_row', 'multiple_answer_per_row'])->nullable();
            $table->boolean('is_required')->default(true);
            $table->boolean('show_result_after_submit')->default(true);
            $table->boolean('allow_next_after_submit')->default(true);
            $table->enum('status', ['draft', 'published'])->default('draft')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_steps');
    }
};
