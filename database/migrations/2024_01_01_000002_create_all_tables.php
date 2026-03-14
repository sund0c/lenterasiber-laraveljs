<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Audit Log ─────────────────────────────────────────
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

        // ── Sessions ──────────────────────────────────────────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // ── Cache ─────────────────────────────────────────────
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

        // ── Konten (Kabar + Komik + Podcast) ──────────────────
        Schema::create('konten', function (Blueprint $table) {
            $table->id();
            $table->enum('label', ['KABAR', 'KOMIK', 'PODCAST']);
            $table->string('title', 255);
            $table->string('slug', 160)->nullable()->unique();       // KABAR only
            $table->string('episode_number', 20)->nullable();        // KOMIK & PODCAST
            $table->string('category', 100)->nullable();             // admin only
            $table->string('excerpt', 400)->nullable();
            $table->longText('content')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->string('external_url', 500)->nullable();         // instagram / audio url
            $table->unsignedSmallInteger('duration_minutes')->nullable(); // PODCAST only
            $table->date('published_date')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('view_count')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();

            $table->index(['label', 'status', 'published_date']);
            $table->index(['label', 'created_by']);
        });

        // ── Workshop ──────────────────────────────────────────
        Schema::create('workshop', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('event_date')->nullable();
            $table->string('location', 255)->nullable();
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->enum('status', ['upcoming', 'ongoing', 'completed', 'cancelled'])->default('upcoming');
            $table->string('thumbnail', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'event_date']);
        });

        // ── Site Settings ─────────────────────────────────────
        Schema::create('site_settings', function (Blueprint $table) {
            $table->string('key', 80)->primary();
            $table->text('value')->nullable();
            $table->string('type', 20)->default('string');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('workshop');
        Schema::dropIfExists('konten');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('audit_logs');
    }
};
