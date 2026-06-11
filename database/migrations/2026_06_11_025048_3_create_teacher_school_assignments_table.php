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
        if (Schema::hasTable('teacher_school_assignments')) {
            return;
        }

        Schema::create('teacher_school_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnDelete();
            $table->foreignId('school_id')->constrained()->restrictOnDelete();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->text('revoke_reason')->nullable();
            $table->timestamps();

            $table->unique(['teacher_id', 'school_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teacher_school_assignments');
    }
};
