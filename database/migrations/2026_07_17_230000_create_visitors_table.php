<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('visitor_code', 64)->nullable();
            $table->string('organization')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 64)->nullable();
            $table->string('purpose')->nullable();
            $table->string('host_name')->nullable();
            $table->dateTime('visited_at')->nullable();
            $table->dateTime('left_at')->nullable();
            $table->date('privacy_notice_signed_at')->nullable();
            $table->boolean('photo_captured')->default(false);
            $table->boolean('badge_issued')->default(false);
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('checked_in')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
