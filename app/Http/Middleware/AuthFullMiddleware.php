<?php

namespace App\Http\Middleware;

use App\Models\AdminUser;
use Closure;
use Illuminate\Http\Request;

/**
 * Ensures fully authenticated (password + TOTP).
 * Also validates session IP binding and idle timeout.
 */
class AuthFullMiddleware
{
    private const IDLE_SECONDS = 3600; // 1 hour

    public function handle(Request $request, Closure $next)
    {
        $userId = session('auth_user_id');
        $authIp = session('auth_ip');
        $authAt = session('auth_at');

        // Not authenticated
        if (!$userId || !$authIp || !$authAt) {
            return redirect()->route('auth.login');
        }

        // IP binding — session hijacking mitigation
        if ($authIp !== $request->ip()) {
            $request->session()->invalidate();
            return redirect()->route('auth.login')
                ->withErrors(['username' => 'Sesi tidak valid. Silakan login kembali.']);
        }

        // Idle timeout
        if (now()->timestamp - $authAt > self::IDLE_SECONDS) {
            $request->session()->invalidate();
            return redirect()->route('auth.login')
                ->withErrors(['username' => 'Sesi habis. Silakan login kembali.']);
        }

        // Load user and inject into request
        $user = AdminUser::find($userId);
        if (!$user) {
            $request->session()->invalidate();
            return redirect()->route('auth.login');
        }

        // Rolling session timestamp
        session(['auth_at' => now()->timestamp]);

        // Make user available in views
        view()->share('currentUser', $user);
        $request->attributes->set('admin_user', $user);

        return $next($request);
    }
}
