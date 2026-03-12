<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AdminUser;
use App\Models\AuditLog;

class ForcePasswordChangeMiddleware
{
    public function handle(Request $request, Closure $next): mixed
    {
        $userId = session('auth_user_id');
        if (!$userId) return $next($request);

        $user = AdminUser::find($userId);
        if (!$user) return $next($request);

        // Route yang diizinkan tanpa ganti password
        $allowed = [
            route('admin.password.change'),
            route('admin.password.update'),
            route('auth.logout'),
        ];

        // ── Cek force_password_change (staf baru / reset) ──
        if ($user->force_password_change) {
            if (!in_array($request->url(), $allowed)) {
                return redirect()->route('admin.password.change')
                    ->with('warning', 'Anda wajib mengganti password sebelum melanjutkan.');
            }
            return $next($request);
        }

        // ── Cek expired password ───────────────────────────
        $months = (int) env('PASSWORD_EXPIRY_MONTHS', 3);
        if ($months > 0) {
            $lastChanged = $user->password_changed_at
                ? \Carbon\Carbon::parse($user->password_changed_at)
                : \Carbon\Carbon::parse($user->created_at);

            if ($lastChanged->addMonths($months)->isPast()) {
                // Set force_password_change agar konsisten
                $user->update(['force_password_change' => true]);

                AuditLog::record('user.password_expired', 'AdminUser', $user->id);

                if (!in_array($request->url(), $allowed)) {
                    return redirect()->route('admin.password.change')
                        ->with('warning', 'Password Anda sudah kadaluarsa (lebih dari ' . $months . ' bulan). Wajib ganti sekarang.');
                }
            }
        }

        return $next($request);
    }
}
