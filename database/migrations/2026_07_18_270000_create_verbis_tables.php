<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('verbis_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('registration_number', 64)->nullable();
            $table->date('registered_at')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 64)->nullable();
            $table->boolean('is_exempt')->default(false);
            $table->text('exemption_reason')->nullable();
            $table->date('last_reviewed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['company_id']);
            $table->index(['tenant_id', 'company_id']);
        });

        Schema::create('verbis_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('verbis_registration_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('processing_activity_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('code', 64)->nullable();
            $table->text('purposes')->nullable();
            $table->text('data_subject_categories')->nullable();
            $table->text('data_categories')->nullable();
            $table->string('legal_basis')->nullable();
            $table->text('recipients')->nullable();
            $table->string('retention_period')->nullable();
            $table->boolean('cross_border_transfer')->default(false);
            $table->string('transfer_countries')->nullable();
            $table->text('security_measures')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verbis_entries');
        Schema::dropIfExists('verbis_registrations');
    }
};
