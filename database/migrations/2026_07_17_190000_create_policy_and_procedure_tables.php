<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policy_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('code', 64)->nullable();
            $table->string('category', 64)->nullable()->index();
            $table->string('version', 32)->default('1.0');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('review_date')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
        });

        Schema::create('procedure_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('policy_document_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('code', 64)->nullable();
            $table->string('category', 64)->nullable()->index();
            $table->string('version', 32)->default('1.0');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->text('steps')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('review_date')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('procedure_documents');
        Schema::dropIfExists('policy_documents');
    }
};
