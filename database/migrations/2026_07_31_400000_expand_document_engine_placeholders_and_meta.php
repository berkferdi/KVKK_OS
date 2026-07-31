<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->string('postal_code', 20)->nullable()->after('district');
            $table->string('country', 100)->nullable()->after('postal_code');
            $table->string('website_url', 255)->nullable()->after('phone');
            $table->string('kep_address', 255)->nullable()->after('email');
            $table->string('kvkk_email', 255)->nullable()->after('kep_address');
            $table->date('founded_at')->nullable()->after('activity_summary');
            $table->string('sgk_registration_number', 64)->nullable()->after('mersis_number');
            $table->string('trade_registry_number', 64)->nullable()->after('sgk_registration_number');
        });

        Schema::table('document_templates', function (Blueprint $table): void {
            $table->string('document_number')->nullable()->after('code');
            $table->string('revision_number', 32)->nullable()->after('version');
            $table->date('revision_date')->nullable()->after('revision_number');
            $table->date('published_at')->nullable()->after('revision_date');
            $table->string('prepared_by')->nullable()->after('published_at');
            $table->string('approved_by')->nullable()->after('prepared_by');
            $table->string('document_status', 32)->default('effective')->after('approved_by');
            $table->string('body_format', 16)->default('html')->after('body');
        });

        Schema::table('generated_documents', function (Blueprint $table): void {
            $table->string('document_number')->nullable()->after('code');
            $table->string('revision_number', 32)->nullable()->after('version');
            $table->date('revision_date')->nullable()->after('revision_number');
            $table->date('published_at')->nullable()->after('revision_date');
            $table->string('prepared_by')->nullable()->after('published_at');
            $table->string('approved_by')->nullable()->after('prepared_by');
            $table->string('document_status', 32)->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table): void {
            $table->dropColumn([
                'postal_code',
                'country',
                'website_url',
                'kep_address',
                'kvkk_email',
                'founded_at',
                'sgk_registration_number',
                'trade_registry_number',
            ]);
        });

        Schema::table('document_templates', function (Blueprint $table): void {
            $table->dropColumn([
                'document_number',
                'revision_number',
                'revision_date',
                'published_at',
                'prepared_by',
                'approved_by',
                'document_status',
                'body_format',
            ]);
        });

        Schema::table('generated_documents', function (Blueprint $table): void {
            $table->dropColumn([
                'document_number',
                'revision_number',
                'revision_date',
                'published_at',
                'prepared_by',
                'approved_by',
                'document_status',
            ]);
        });
    }
};
