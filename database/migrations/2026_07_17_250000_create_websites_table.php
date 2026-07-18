<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('websites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('website_code', 64)->nullable();
            $table->string('url');
            $table->string('platform', 64)->nullable();
            $table->string('hosting_provider')->nullable();
            $table->boolean('has_contact_form')->default(false);
            $table->boolean('has_newsletter')->default(false);
            $table->boolean('has_user_accounts')->default(false);
            $table->boolean('has_payment')->default(false);
            $table->boolean('ssl_enabled')->default(true);
            $table->boolean('privacy_policy_published')->default(false);
            $table->string('privacy_policy_url')->nullable();
            $table->date('privacy_policy_published_at')->nullable();
            $table->boolean('uses_cookies')->default(false);
            $table->text('data_collected')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('active')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('websites');
    }
};
