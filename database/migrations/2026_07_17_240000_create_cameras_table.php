<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('camera_code', 64)->nullable();
            $table->string('camera_type', 32)->default('indoor')->index();
            $table->string('location')->nullable();
            $table->string('coverage_area')->nullable();
            $table->boolean('is_recording')->default(true);
            $table->boolean('records_audio')->default(false);
            $table->unsignedSmallInteger('retention_days')->nullable();
            $table->string('storage_location')->nullable();
            $table->boolean('notice_posted')->default(false);
            $table->date('notice_posted_at')->nullable();
            $table->date('installed_at')->nullable();
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
        Schema::dropIfExists('cameras');
    }
};
