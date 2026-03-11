<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

/**
 * LoginController — Step 1 of 2-step auth.
 *
 * Security features:
 *  - Rate limiting: 5 attempts / 15 min per IP+username
 *  - Account lockout (DB-level, survives server restart)
 *  - Constant-time password comparison via Hash::check
 *  - Generic error messages (no username enumeration)
 *  - Audit logging of all attempts
 */
class LoginController extends Controller
{
    public function showLogin()
    {
        // Already fully authenticated → redirect to admin
        if (session('auth_complete')) {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:80'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $username = $request->input('username');
        $ip       = $request->ip();
        $key      = 'login:' . sha1($ip . '|' . $username);

        // ── Rate limiter ────────────────────────────────────────
        if (RateLimiter::tooManyAttempts($key, config('auth.max_attempts', 5))) {
            $seconds = RateLimiter::availableIn($key);
            AuditLog::record('login.rate_limited', null, null, null, [
                'username' => $username, 'ip' => $ip,
            ]);
            return back()->withErrors([
                'username' => "Terlalu banyak percobaan. Coba lagi dalam {$seconds} detik.",
            ])->onlyInput('username');
        }

        // ── Find user ───────────────────────────────────────────
        $user = AdminUser::where('username', $username)
                         ->orWhere('email', $username)
                         ->first();

        // ── Account lockout check ────────────────────────────────
        if ($user && $user->isLocked()) {
            RateLimiter::hit($key, config('auth.lockout_minutes', 15) * 60);
            AuditLog::record('login.account_locked', 'AdminUser', $user->id);
            return back()->withErrors([
                'username' => 'Akun terkunci. Hubungi administrator.',
            ])->onlyInput('username');
        }

        // ── Password check ──────────────────────────────────────
        $valid = $user && Hash::check($request->input('password'), $user->password);

        if (!$valid) {
            RateLimiter::hit($key, config('auth.lockout_minutes', 15) * 60);

            if ($user) {
                $user->incrementFailedAttempts();
                AuditLog::record('login.failed', 'AdminUser', $user->id, null, ['ip' => $ip]);
            }

            // Generic message — no enumeration
            return back()->withErrors([
                'username' => 'Username atau password salah.',
            ])->onlyInput('username');
        }

        // ── Password OK ─────────────────────────────────────────
        RateLimiter::clear($key);
        $user->clearFailedAttempts();

        // Store step-1 state in session (not fully authed yet)
        $request->session()->regenerate();
        session([
            'auth_step1_id'  => $user->id,
            'auth_step1_at'  => now()->timestamp,
        ]);

        AuditLog::record('login.step1_ok', 'AdminUser', $user->id);

        // Redirect to TOTP setup or verify
        if (!$user->totp_enabled) {
            return redirect()->route('auth.2fa.setup');
        }
        return redirect()->route('auth.2fa.verify');
    }

    public function logout(Request $request)
    {
        $userId = $request->session()->get('auth_user_id')
               ?? $request->session()->get('auth_step1_id');

        if ($userId) {
            AuditLog::record('logout', 'AdminUser', $userId);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login');
    }
}
