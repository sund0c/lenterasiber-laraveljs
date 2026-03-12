<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StafPasswordMail;
use App\Models\AdminUser;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = AdminUser::orderBy('role')
            ->orderBy('full_name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.form', ['user' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'username'  => ['required', 'string', 'max:60', 'unique:admin_users,username', 'regex:/^[a-z0-9_]+$/'],
            'email'     => ['required', 'email', 'max:255', 'unique:admin_users,email'],
        ]);

        // Generate password acak yang kuat
        $plainPassword = $this->generatePassword();

        $user = AdminUser::create([
            'full_name'             => $request->full_name,
            'username'              => $request->username,
            'email'                 => $request->email,
            'password'              => Hash::make($plainPassword, ['rounds' => 13]),
            'role'                  => 'staf',
            'force_password_change' => true,
            'totp_enabled'          => false,
        ]);

        // Kirim email password awal
        try {
            Mail::to($user->email)->send(new StafPasswordMail(
                fullName: $user->full_name,
                username: $user->username,
                plainPassword: $plainPassword,
                loginUrl: url(config('auth.admin_login_path', '/portal-internal-x83fj9/login')),
            ));
            $emailSent = true;
        } catch (\Exception $e) {
            $emailSent = false;
            \Log::error('Gagal kirim email staf: ' . $e->getMessage());
        }

        AuditLog::record('user.create', 'AdminUser', $user->id, null, [
            'username' => $user->username,
            'email'    => $user->email,
        ]);

        $msg = 'Akun staf berhasil dibuat.';
        if (!$emailSent) {
            $msg .= ' ⚠️ Email gagal terkirim — password awal: <code>' . e($plainPassword) . '</code>';
            return redirect()->route('admin.users.index')->with('warning', $msg);
        }

        return redirect()->route('admin.users.index')->with('success', $msg . ' Password dikirim ke ' . $user->email);
    }

    public function destroy(int $id)
    {
        $user = AdminUser::findOrFail($id);
        if ($user->id === session('auth_user_id')) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }
        $user->delete(); // sekarang hard delete karena SoftDeletes sudah dihapus
        AuditLog::record('user.delete', 'AdminUser', $id);
        return redirect()->route('admin.users.index')->with('success', 'Akun staf dihapus.');
    }

    public function resetPassword(int $id)
    {
        $user = AdminUser::findOrFail($id);

        $plainPassword = $this->generatePassword();

        $user->update([
            'password'              => Hash::make($plainPassword, ['rounds' => 13]),
            'force_password_change' => true,
            'failed_attempts'       => 0,
            'locked_until'          => null,
        ]);

        try {
            Mail::to($user->email)->send(new StafPasswordMail(
                fullName: $user->full_name,
                username: $user->username,
                plainPassword: $plainPassword,
                loginUrl: url(config('auth.admin_login_path', '/portal-internal-x83fj9/login')),
            ));
            $emailSent = true;
        } catch (\Exception $e) {
            $emailSent = false;
        }

        AuditLog::record('user.reset_password', 'AdminUser', $id);

        if (!$emailSent) {
            return back()->with('warning', 'Password direset. ⚠️ Email gagal — password baru: <code>' . e($plainPassword) . '</code>');
        }

        return back()->with('success', 'Password direset dan dikirim ke ' . $user->email);
    }

    private function generatePassword(): string
    {
        // Format: Xxxx-0000-xxxx (mudah dibaca, memenuhi syarat kompleksitas)
        $upper  = strtoupper(Str::random(1));
        $lower1 = strtolower(Str::random(3));
        $digits = rand(1000, 9999);
        $lower2 = strtolower(Str::random(4));
        $symbol = ['@', '#', '!', '$'][rand(0, 3)];

        return $upper . $lower1 . $symbol . $digits . '-' . $lower2;
    }
}
