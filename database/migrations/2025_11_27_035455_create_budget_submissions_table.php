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
        Schema::create('budget_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('budget_request_id')->constrained('budget_requests')->onDelete('cascade');
            $table->text('submission_content');
            $table->string('document_path')->nullable();
            $table->enum('status', ['pending', 'department_reviewed', 'admin_reviewed'])->default('pending');
            $table->text('department_feedback')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_submissions');
    }
};
