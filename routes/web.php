<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TotpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KabarController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Admin\KomikController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication — obscured login URL
|--------------------------------------------------------------------------
*/

$loginPath = ltrim(env('ADMIN_LOGIN_PATH', '/portal-internal-x83fj9/login'), '/');
$loginBase = dirname($loginPath);

Route::prefix($loginBase)->name('auth.')->group(function () use ($loginPath) {
    $slug = basename($loginPath);

    Route::get($slug,        [LoginController::class, 'showLogin'])->name('login');
    Route::post($slug,       [LoginController::class, 'login'])->name('login.post');
    Route::get('logout',     [LoginController::class, 'logout'])->name('logout');

    Route::middleware('auth.step1')->group(function () {
        Route::get('2fa/verify',  [TotpController::class, 'showVerify'])->name('2fa.verify');
        Route::post('2fa/verify', [TotpController::class, 'verify'])->name('2fa.verify.post');
        Route::get('2fa/setup',   [TotpController::class, 'showSetup'])->name('2fa.setup');
        Route::post('2fa/setup',  [TotpController::class, 'setup'])->name('2fa.setup.post');
        Route::get('2fa/backup',  [TotpController::class, 'showBackup'])->name('2fa.backup');
        Route::post('2fa/backup', [TotpController::class, 'backup'])->name('2fa.backup.post');
    });
});

/*
|--------------------------------------------------------------------------
| Admin panel — fully authenticated (password + TOTP)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth.full', 'auth.audit'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Ganti Password (wajib staf baru) — SEBELUM resource routes
    Route::get('password/change',  [PasswordController::class, 'showChange'])->name('password.change');
    Route::post('password/change', [PasswordController::class, 'update'])->name('password.update');

    // Kabar
    Route::get('kabar/{id}/show', [KabarController::class, 'show'])->name('kabar.show');
    Route::resource('kabar', KabarController::class)->except(['show']);

    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin')->group(function () {
        Route::post('kabar/{id}/publish',   [KabarController::class, 'publish'])->name('kabar.publish');
        Route::post('kabar/{id}/unpublish', [KabarController::class, 'unpublish'])->name('kabar.unpublish');
    });

    // Layanan
    Route::resource('layanan',  LayananController::class)->except(['show']);

    // Workshop
    Route::resource('workshop', WorkshopController::class)->except(['show']);

    // Komik
    Route::get('komik/{id}/show', [KomikController::class, 'show'])->name('komik.show');
    Route::resource('komik', KomikController::class)->except(['show']);

    // Podcast
    Route::get('podcast/{id}/show', [PodcastController::class, 'show'])->name('podcast.show');
    Route::resource('podcast', PodcastController::class)->except(['show']);

    // Settings
    Route::get('settings',  [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    // Audit log
    Route::get('audit', [AuditController::class, 'index'])->name('audit.index');

    // Manajemen User Staf (admin only)
    Route::middleware(\App\Http\Middleware\RoleMiddleware::class . ':admin')->group(function () {
        Route::get('users',                        [AdminUserController::class, 'index'])->name('users.index');
        Route::get('users/create',                 [AdminUserController::class, 'create'])->name('users.create');
        Route::post('users',                       [AdminUserController::class, 'store'])->name('users.store');
        Route::delete('users/{id}',                [AdminUserController::class, 'destroy'])->name('users.destroy');
        Route::post('users/{id}/reset-password',   [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    });
});

/*
|--------------------------------------------------------------------------
| Public API
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->group(function () {
    Route::get('content',   [App\Http\Controllers\Api\ContentController::class, 'index'])->name('content');
    Route::post('contact',  [App\Http\Controllers\Api\ContactController::class, 'store'])->name('contact');
});

/*
|--------------------------------------------------------------------------
| Frontend catch-all
|--------------------------------------------------------------------------
*/
Route::get('/{any?}', function () {
    return view('frontend');
})->where('any', '^(?!admin|api|portal-internal).*')->name('frontend');
