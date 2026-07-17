<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compliance_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('code', 64);
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('priority')->default(100);
            $table->json('conditions');
            $table->json('actions');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->index(['is_active', 'priority']);
        });

        Schema::create('analysis_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('triggered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('status', 32)->default('pending')->index();
            $table->json('input_snapshot')->nullable();
            $table->json('result_summary')->nullable();
            $table->unsignedInteger('matched_rules_count')->default(0);
            $table->unsignedInteger('findings_count')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'company_id']);
        });

        Schema::create('analysis_findings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analysis_run_id')->constrained()->cascadeOnDelete();
            $table->foreignId('compliance_rule_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 64)->index();
            $table->string('code', 64)->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity', 32)->default('medium')->index();
            $table->json('payload')->nullable();
            $table->timestamps();

            $table->index(['analysis_run_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analysis_findings');
        Schema::dropIfExists('analysis_runs');
        Schema::dropIfExists('compliance_rules');
    }
};
