<?php
// app/Http/Middleware/AuthStep1Middleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Ensures step-1 (password) is complete before allowing TOTP pages.
 */
class AuthStep1Middleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('auth_step1_id')) {
            return redirect()->route('auth.login');
        }

        // If already fully authenticated, skip TOTP pages
        if (session('auth_user_id')) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
