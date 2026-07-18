<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_breaches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('breach_code', 64)->nullable();
            $table->string('breach_type', 32)->default('confidentiality')->index();
            $table->string('severity', 32)->default('medium')->index();
            $table->dateTime('discovered_at')->nullable();
            $table->dateTime('occurred_at')->nullable();
            $table->dateTime('authority_notification_due_at')->nullable();
            $table->dateTime('authority_notified_at')->nullable();
            $table->boolean('subjects_notification_required')->default(false);
            $table->dateTime('subjects_notified_at')->nullable();
            $table->unsignedInteger('affected_subjects_count')->nullable();
            $table->text('data_categories')->nullable();
            $table->text('description')->nullable();
            $table->text('consequences')->nullable();
            $table->text('measures_taken')->nullable();
            $table->text('root_cause')->nullable();
            $table->string('assigned_to_name')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('investigating')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'authority_notification_due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_breaches');
    }
};
