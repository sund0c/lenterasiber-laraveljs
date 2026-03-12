<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function showChange()
    {
        return view('admin.password.change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => [
                'required',
                'string',
                'min:12',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            ],
        ], [
            'password.regex' => 'Password harus mengandung huruf besar, huruf kecil, angka, dan simbol.',
            'password.min'   => 'Password minimal 12 karakter.',
        ]);

        $user = AdminUser::findOrFail(session('auth_user_id'));

        // ── Cek password saat ini ──────────────────────────
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak benar.']);
        }

        // ── Cek tidak sama dengan password sekarang ────────
        if (Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Password baru tidak boleh sama dengan password saat ini.']);
        }

        // ── Cek riwayat 2 password sebelumnya ─────────────
        $history = json_decode($user->password_history ?? '[]', true) ?? [];
        foreach ($history as $oldHash) {
            if (Hash::check($request->password, $oldHash)) {
                return back()->withErrors(['password' => 'Password tidak boleh sama dengan 2 password sebelumnya.']);
            }
        }

        // ── Simpan password lama ke history (maks 2) ──────
        array_unshift($history, $user->password); // tambah ke depan
        $history = array_slice($history, 0, 2);   // ambil 2 terbaru saja

        $needsTotp = !$user->totp_enabled;
        $userId    = $user->id;

        // ── Update password ────────────────────────────────
        $user->update([
            'password'              => Hash::make($request->password, ['rounds' => 13]),
            'password_history'      => json_encode($history),
            'password_changed_at'   => now(),
            'force_password_change' => false,
        ]);

        AuditLog::record('user.password_changed', 'AdminUser', $userId);

        // ── DESTROY SESSION (keamanan) ─────────────────────
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($needsTotp) {
            session([
                'auth_step1_id' => $userId,
                'auth_step1_at' => now()->timestamp,
            ]);
            return redirect()->route('auth.2fa.setup')
                ->with('success', 'Password berhasil diganti. Sekarang aktifkan 2FA.');
        }

        return redirect()->route('auth.login')
            ->with('success', 'Password berhasil diganti. Silakan login kembali.');
    }
}
