<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AdminUser;

class RoleMiddleware
{
    /**
     * Cek role user yang sedang login.
     * Usage di route: middleware('role:admin')
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        $userId = session('auth_user_id');
        if (!$userId) {
            return redirect()->route('auth.login');
        }

        $user = AdminUser::find($userId);
        if (!$user || $user->role !== $role) {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk ' . $role . '.');
        }

        return $next($request);
    }
}
