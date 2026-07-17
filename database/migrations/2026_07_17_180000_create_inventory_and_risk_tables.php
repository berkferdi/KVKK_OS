<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processing_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('code', 64)->nullable();
            $table->string('purpose')->nullable();
            $table->text('description')->nullable();
            $table->json('data_categories')->nullable();
            $table->json('data_subject_categories')->nullable();
            $table->string('legal_basis', 64)->nullable();
            $table->string('legal_basis_detail')->nullable();
            $table->json('recipients')->nullable();
            $table->string('retention_period')->nullable();
            $table->boolean('cross_border_transfer')->default(false);
            $table->string('transfer_countries')->nullable();
            $table->text('security_measures')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
        });

        Schema::create('risk_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('processing_activity_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('code', 64)->nullable();
            $table->text('description')->nullable();
            $table->string('asset_type', 64)->nullable();
            $table->string('threat')->nullable();
            $table->string('vulnerability')->nullable();
            $table->unsignedTinyInteger('likelihood')->default(1);
            $table->unsignedTinyInteger('impact')->default(1);
            $table->unsignedSmallInteger('score')->default(1);
            $table->string('risk_level', 32)->default('low')->index();
            $table->text('existing_controls')->nullable();
            $table->text('mitigation_plan')->nullable();
            $table->string('owner_name')->nullable();
            $table->date('review_date')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('open')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_assessments');
        Schema::dropIfExists('processing_activities');
    }
};
