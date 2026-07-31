<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->after('id');
            $table->string('phone', 32)->nullable()->after('email');
            $table->boolean('is_active')->default(true)->after('password');
            $table->boolean('is_super_admin')->default(false)->after('is_active');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'uuid',
                'phone',
                'is_active',
                'is_super_admin',
                'last_login_at',
                'deleted_at',
            ]);
        });
    }
};
