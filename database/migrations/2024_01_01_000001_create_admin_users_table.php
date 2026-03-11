<?php
// database/migrations/2024_01_01_000001_create_admin_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 60)->unique();
            $table->string('full_name', 150);
            $table->string('email', 255)->unique();
            $table->string('password');                    // bcrypt cost 13
            $table->string('totp_secret', 64)->nullable(); // Base32, encrypted at rest
            $table->boolean('totp_enabled')->default(false);
            $table->text('backup_codes')->nullable();      // JSON array, bcrypt-hashed, encrypted
            $table->unsignedSmallInteger('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable(); // supports IPv6
            $table->boolean('force_password_change')->default(false);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['username', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_users');
    }
};
