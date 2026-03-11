<?php
// app/Http/Controllers/Admin/SettingsController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = DB::table('site_settings')->get()->keyBy('key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'workshop_count' => ['nullable','integer','min:0'],
            'asn_count'      => ['nullable','integer','min:0'],
            'article_count'  => ['nullable','integer','min:0'],
        ]);

        foreach ($data as $key => $value) {
            DB::table('site_settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        AuditLog::record('settings.update');
        return back()->with('success', 'Pengaturan disimpan.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password'     => [
                'required', 'min:12', 'confirmed',
                'regex:/[A-Z]/', 'regex:/[a-z]/', 'regex:/[0-9]/', 'regex:/[\W]/',
            ],
        ], [
            'new_password.regex' => 'Password harus mengandung huruf besar, kecil, angka, dan simbol.',
        ]);

        $user = AdminUser::findOrFail(session('auth_user_id'));

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->password = $request->new_password;
        $user->save();

        AuditLog::record('settings.password_changed', 'AdminUser', $user->id);
        return back()->with('success', 'Password berhasil diubah.');
    }
}
