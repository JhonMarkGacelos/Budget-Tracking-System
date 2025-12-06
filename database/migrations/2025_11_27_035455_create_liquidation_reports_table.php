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
        Schema::create('liquidation_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('budget_request_id')->constrained('budget_requests')->onDelete('cascade');
            $table->text('report_content');
            $table->enum('status', ['pending', 'department_reviewed', 'admin_reviewed'])->default('pending');
            $table->text('department_feedback')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('liquidation_reports');
    }
};
