<?php
// database/migrations/2024_01_01_000002_create_core_tables.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Audit log ─────────────────────────────────────────
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_user_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->string('action', 120);
            $table->string('entity_type', 80)->nullable();
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['action', 'created_at']);
            $table->index(['admin_user_id', 'created_at']);
        });

        // ── Sessions (database driver) ────────────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ── Cache (database driver) ───────────────────────────
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // ── Rate limiter (database) ───────────────────────────
        // Laravel uses cache table for RateLimiter

        // ── Kabar Lentera (artikel/berita) ────────────────────
        Schema::create('kabar', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 160)->unique();
            $table->string('title', 255);
            $table->string('excerpt', 400)->nullable();
            $table->longText('content');
            $table->string('category', 40)->default('edukasi');
            $table->enum('status', ['draft','published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedSmallInteger('read_minutes')->default(3);
            $table->string('thumbnail', 255)->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_desc', 160)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
        });

        // ── Layanan ───────────────────────────────────────────
        Schema::create('layanan', function (Blueprint $table) {
            $table->id();
            $table->string('icon', 80)->nullable();
            $table->string('title', 150);
            $table->string('short_desc', 300)->nullable();
            $table->longText('full_content')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Workshop ──────────────────────────────────────────
        Schema::create('workshop', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('event_date')->nullable();
            $table->string('location', 255)->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->enum('status', ['upcoming','ongoing','completed','cancelled'])->default('upcoming');
            $table->string('thumbnail', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'event_date']);
        });

        // ── Komik ─────────────────────────────────────────────
        Schema::create('komik', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('file_path', 255)->nullable(); // PDF/CBZ path
            $table->boolean('is_published')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        // ── Podcast ───────────────────────────────────────────
        Schema::create('podcast', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->string('episode_number', 20)->nullable();
            $table->string('audio_url', 500)->nullable(); // external Spotify link
            $table->string('thumbnail', 255)->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->boolean('is_published')->default(false);
            $table->date('published_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_date']);
        });

        // ── Pesan masuk (contact form) ────────────────────────
        Schema::create('pesan_masuk', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('email', 255);
            $table->string('instansi', 255)->nullable();
            $table->string('subjek', 255);
            $table->text('pesan');
            $table->ipAddress('ip_address')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->foreignId('read_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
        });

        // ── Site settings ─────────────────────────────────────
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key', 80)->primary();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string'); // string, int, bool, json
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('pesan_masuk');
        Schema::dropIfExists('podcast');
        Schema::dropIfExists('komik');
        Schema::dropIfExists('workshop');
        Schema::dropIfExists('layanan');
        Schema::dropIfExists('kabar');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('audit_logs');
    }
};
