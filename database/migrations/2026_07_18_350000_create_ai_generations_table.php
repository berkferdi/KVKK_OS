<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('analysis_run_id')->nullable()->constrained('analysis_runs')->nullOnDelete();
            $table->foreignId('document_template_id')->nullable()->constrained('document_templates')->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('purpose', 64)->index();
            $table->string('driver', 32)->default('heuristic');
            $table->string('model')->nullable();
            $table->string('prompt_hash', 64)->nullable();
            $table->json('input_snapshot')->nullable();
            $table->longText('output_text')->nullable();
            $table->string('status', 32)->default('pending')->index();
            $table->unsignedInteger('tokens_used')->nullable();
            $table->text('error_message')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generations');
    }
};
