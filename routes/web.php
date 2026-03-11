<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TotpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KabarController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\WorkshopController;
use App\Http\Controllers\Admin\KomikController;
use App\Http\Controllers\Admin\PodcastController;
use App\Http\Controllers\Admin\PesanController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\AuditController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication — obscured login URL
|--------------------------------------------------------------------------
*/
$loginPath = ltrim(env('ADMIN_LOGIN_PATH', '/portal-internal-x83fj9/login'), '/');
$loginBase = dirname($loginPath);  // portal-internal-x83fj9

Route::prefix($loginBase)->name('auth.')->group(function () use ($loginPath) {
    $slug = basename($loginPath); // login

    Route::get($slug,          [LoginController::class, 'showLogin'])->name('login');
    Route::post($slug,         [LoginController::class, 'login'])->name('login.post');
    Route::get('logout',       [LoginController::class, 'logout'])->name('logout');

    // 2FA verification (step 2 — after password OK)
    Route::middleware('auth.step1')->group(function () {
        Route::get('2fa/verify',   [TotpController::class, 'showVerify'])->name('2fa.verify');
        Route::post('2fa/verify',  [TotpController::class, 'verify'])->name('2fa.verify.post');
        Route::get('2fa/setup',    [TotpController::class, 'showSetup'])->name('2fa.setup');
        Route::post('2fa/setup',   [TotpController::class, 'setup'])->name('2fa.setup.post');
        Route::get('2fa/backup',   [TotpController::class, 'showBackup'])->name('2fa.backup');
        Route::post('2fa/backup',  [TotpController::class, 'backup'])->name('2fa.backup.post');
    });
});

/*
|--------------------------------------------------------------------------
| Admin panel — fully authenticated (password + TOTP)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth.full', 'auth.audit'])->group(function () {

    Route::get('/',                    [DashboardController::class, 'index'])->name('dashboard');

    // Kabar Lentera (berita/artikel)
    Route::resource('kabar',           KabarController::class)->except(['show']);

    // Layanan
    Route::resource('layanan',         LayananController::class)->except(['show']);

    // Workshop
    Route::resource('workshop',        WorkshopController::class)->except(['show']);

    // Komik
    Route::resource('komik',           KomikController::class)->except(['show']);

    // Podcast
    Route::resource('podcast',         PodcastController::class)->except(['show']);

    // Pesan masuk (read-only + delete)
    Route::get('pesan',                [PesanController::class, 'index'])->name('pesan.index');
    Route::get('pesan/{id}',           [PesanController::class, 'show'])->name('pesan.show');
    Route::delete('pesan/{id}',        [PesanController::class, 'destroy'])->name('pesan.destroy');

    // Settings
    Route::get('settings',             [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings',            [SettingsController::class, 'update'])->name('settings.update');
    Route::post('settings/password',   [SettingsController::class, 'changePassword'])->name('settings.password');

    // Audit log (view only)
    Route::get('audit',                [AuditController::class, 'index'])->name('audit.index');
});

/*
|--------------------------------------------------------------------------
| Public API — for frontend SPA
|--------------------------------------------------------------------------
*/
Route::prefix('api')->name('api.')->group(function () {
    Route::get('content',              [App\Http\Controllers\Api\ContentController::class, 'index'])->name('content');
    Route::post('contact',             [App\Http\Controllers\Api\ContactController::class, 'store'])->name('contact');
});

/*
|--------------------------------------------------------------------------
| Frontend catch-all — serve SPA
|--------------------------------------------------------------------------
*/
Route::get('/{any?}', function () {
    return view('frontend');
})->where('any', '^(?!admin|api|portal-internal).*')->name('frontend');
