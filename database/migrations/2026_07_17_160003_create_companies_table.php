<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('trade_name');
            $table->string('title')->nullable();
            $table->string('tax_number', 20)->nullable()->index();
            $table->string('tax_office')->nullable();
            $table->string('mersis_number', 32)->nullable();
            $table->string('nace_code', 32)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 32)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('authorized_person')->nullable();
            $table->string('authorized_title')->nullable();
            $table->text('activity_summary')->nullable();
            $table->boolean('has_camera')->default(false);
            $table->boolean('has_website')->default(false);
            $table->boolean('has_cookies')->default(false);
            $table->unsignedInteger('employee_count')->nullable();
            $table->string('status', 32)->default('draft')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'status']);
            $table->index(['tenant_id', 'trade_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
