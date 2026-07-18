<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();
            $table->string('title');
            $table->string('training_code', 64)->nullable();
            $table->string('training_type', 32)->default('awareness')->index();
            $table->string('delivery_method', 32)->default('in_person');
            $table->dateTime('planned_at')->nullable();
            $table->dateTime('conducted_at')->nullable();
            $table->dateTime('next_training_due_at')->nullable();
            $table->string('trainer_name')->nullable();
            $table->unsignedInteger('participant_count')->nullable();
            $table->text('participant_names')->nullable();
            $table->text('topics')->nullable();
            $table->text('materials')->nullable();
            $table->text('attendance_notes')->nullable();
            $table->text('notes')->nullable();
            $table->string('source', 32)->default('manual');
            $table->string('status', 32)->default('planned')->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tenant_id', 'company_id']);
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'next_training_due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_records');
    }
};
