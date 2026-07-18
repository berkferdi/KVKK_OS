<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('employee_code', 64)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('department')->nullable();
            $table->string('job_title')->nullable();
            $table->string('employment_type', 32)->default('full_time')->index();
            $table->date('hired_at')->nullable();
            $table->date('left_at')->nullable();
            $table->date('privacy_notice_signed_at')->nullable();
            $table->date('confidentiality_signed_at')->nullable();
            $table->date('training_completed_at')->nullable();
            $table->boolean('has_system_access')->default(false);
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'last_name', 'first_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
