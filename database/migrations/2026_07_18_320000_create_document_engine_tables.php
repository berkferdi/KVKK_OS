<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('code', 64);
            $table->string('title');
            $table->string('category', 32)->default('other')->index();
            $table->text('description')->nullable();
            $table->longText('body');
            $table->string('storage_path')->nullable();
            $table->json('output_formats')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('is_active')->default(true);
            $table->string('source', 32)->default('manual');
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tenant_id', 'code']);
            $table->index(['tenant_id', 'is_active']);
        });

        Schema::create('generated_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_template_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('code', 64)->nullable();
            $table->string('status', 32)->default('generated')->index();
            $table->longText('rendered_content')->nullable();
            $table->json('placeholder_snapshot')->nullable();
            $table->json('missing_placeholders')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('format', 16)->default('text');
            $table->string('file_path')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedInteger('version')->default(1);
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'document_template_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_documents');
        Schema::dropIfExists('document_templates');
    }
};
