<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_subject_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('application_code', 64)->nullable();
            $table->string('applicant_name');
            $table->string('applicant_email')->nullable();
            $table->string('applicant_phone', 64)->nullable();
            $table->string('request_type', 32)->default('access')->index();
            $table->string('channel', 32)->default('email')->index();
            $table->date('received_at')->nullable();
            $table->date('due_at')->nullable();
            $table->date('responded_at')->nullable();
            $table->text('request_summary')->nullable();
            $table->text('response_summary')->nullable();
            $table->boolean('identity_verified')->default(false);
            $table->string('assigned_to_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('received')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_subject_applications');
    }
};
