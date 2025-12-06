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
        Schema::create('budget_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'department_approved', 'department_rejected', 'admin_approved', 'admin_rejected'])->default('pending');
            $table->text('department_feedback')->nullable();
            $table->text('admin_feedback')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('department_reviewed_at')->nullable();
            $table->timestamp('admin_reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('budget_requests');
    }
};
