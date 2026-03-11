<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AuditLog;
use App\Services\TotpService;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class TotpController extends Controller
{
    public function __construct(
        private TotpService $totp,
    ) {}

    public function showSetup(Request $request)
    {
        $user = $this->step1User($request);
        if (!$user) return redirect()->route('auth.login');

        if (!$user->totp_secret) {
            $user->totp_secret = $this->totp->generateSecret();
            $user->saveQuietly();
        }

        $uri   = $this->totp->getUri($user->totp_secret, $user->email);
        $qrSvg = $this->generateQrSvg($uri);

        return view('auth.totp-setup', [
            'user'   => $user,
            'secret' => $user->totp_secret,
            'qrSvg'  => $qrSvg,
        ]);
    }

    public function setup(Request $request)
    {
        $user = $this->step1User($request);
        if (!$user) return redirect()->route('auth.login');

        $request->validate([
            'totp_code' => ['required', 'string', 'digits:6'],
        ]);

        if (!$this->checkTotpRateLimit($request)) {
            return back()->withErrors(['totp_code' => 'Terlalu banyak percobaan. Tunggu beberapa menit.']);
        }

        if (!$this->totp->verify($user->totp_secret, $request->input('totp_code'))) {
            $this->hitTotpRateLimit($request);
            AuditLog::record('2fa.setup_failed', 'AdminUser', $user->id);
            return back()->withErrors(['totp_code' => 'Kode tidak valid. Pastikan waktu perangkat sinkron.']);
        }

        $codes              = $this->totp->generateBackupCodes();
        $user->totp_enabled = true;
        $user->backup_codes = $codes['hashed'];
        $user->save();

        AuditLog::record('2fa.setup_completed', 'AdminUser', $user->id);
        session(['backup_codes_display' => $codes['plain']]);

        return redirect()->route('auth.2fa.backup');
    }

    public function showBackup(Request $request)
    {
        $user = $this->step1User($request);
        if (!$user) return redirect()->route('auth.login');

        $codes = session()->pull('backup_codes_display');
        if (!$codes) return redirect()->route('auth.2fa.verify');

        return view('auth.backup-codes', ['codes' => $codes]);
    }

    public function backup(Request $request)
    {
        return redirect()->route('auth.2fa.verify');
    }

    public function showVerify(Request $request)
    {
        $user = $this->step1User($request);
        if (!$user) return redirect()->route('auth.login');
        if (!$user->totp_enabled) return redirect()->route('auth.2fa.setup');

        return view('auth.totp-verify', ['user' => $user]);
    }

    public function verify(Request $request)
    {
        $user = $this->step1User($request);
        if (!$user) return redirect()->route('auth.login');

        $request->validate([
            'totp_code' => ['required', 'string', 'max:20'],
        ]);

        if (!$this->checkTotpRateLimit($request)) {
            return back()->withErrors(['totp_code' => 'Terlalu banyak percobaan. Tunggu beberapa menit.']);
        }

        $code    = trim($request->input('totp_code'));
        $success = false;

        if (preg_match('/^\d{6}$/', $code)) {
            $success = $this->totp->verify($user->totp_secret, $code);
        }

        if (!$success) {
            $remaining = $this->totp->verifyBackupCode($code, $user->backup_codes ?? []);
            if ($remaining !== null) {
                $user->backup_codes = $remaining;
                $user->saveQuietly();
                $success = true;
                AuditLog::record('2fa.backup_code_used', 'AdminUser', $user->id);
            }
        }

        if (!$success) {
            $this->hitTotpRateLimit($request);
            AuditLog::record('2fa.verify_failed', 'AdminUser', $user->id, null, ['ip' => $request->ip()]);
            return back()->withErrors(['totp_code' => 'Kode tidak valid.']);
        }

        $this->clearTotpRateLimit($request);
        $request->session()->regenerate();

        session()->forget(['auth_step1_id', 'auth_step1_at']);
        session([
            'auth_user_id' => $user->id,
            'auth_ip'      => $request->ip(),
            'auth_at'      => now()->timestamp,
        ]);

        $user->update([
            'last_login_at'   => now(),
            'last_login_ip'   => $request->ip(),
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);

        AuditLog::record('login.success', 'AdminUser', $user->id, null, ['ip' => $request->ip()]);

        return redirect()->route('admin.dashboard');
    }

    private function generateQrSvg(string $uri): string
    {
        $renderer = new ImageRenderer(
            new RendererStyle(200),
            new SvgImageBackEnd()
        );
        return (new Writer($renderer))->writeString($uri);
    }

    private function step1User(Request $request): ?AdminUser
    {
        $id = session('auth_step1_id');
        $at = session('auth_step1_at');
        if (!$id || !$at) return null;
        if (now()->timestamp - $at > 600) {
            session()->forget(['auth_step1_id', 'auth_step1_at']);
            return null;
        }
        return AdminUser::find($id);
    }

    private function totpKey(Request $request): string
    {
        return '2fa:' . sha1($request->ip() . '|' . session('auth_step1_id'));
    }

    private function checkTotpRateLimit(Request $request): bool
    {
        return !RateLimiter::tooManyAttempts($this->totpKey($request), 5);
    }

    private function hitTotpRateLimit(Request $request): void
    {
        RateLimiter::hit($this->totpKey($request), 600);
    }

    private function clearTotpRateLimit(Request $request): void
    {
        RateLimiter::clear($this->totpKey($request));
    }
}
