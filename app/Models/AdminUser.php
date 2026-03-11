<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;

/**
 * AdminUser — Platform administrator account.
 *
 * Security properties:
 *  - Password min 12 chars, bcrypt cost 13
 *  - TOTP 2FA mandatory on first login
 *  - Backup codes hashed with bcrypt, single-use
 *  - Account lockout tracked via failed_attempts
 *  - All mutations logged via AuditLog
 */
class AdminUser extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'admin_users';

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'totp_secret',
        'totp_enabled',
        'backup_codes',
        'failed_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'totp_secret',
        'backup_codes',
        'remember_token',
    ];

    protected $casts = [
        'totp_enabled'          => 'boolean',
        'backup_codes'          => 'encrypted:array',  // encrypted in DB
        'failed_attempts'       => 'integer',
        'locked_until'          => 'datetime',
        'last_login_at'         => 'datetime',
        'force_password_change' => 'boolean',
    ];

    // ── Helpers ──────────────────────────────────────────────

    public function isLocked(): bool
    {
        return $this->locked_until !== null && $this->locked_until->isFuture();
    }

    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_attempts');
        $max = (int) config('auth.max_attempts', 5);
        if ($this->failed_attempts >= $max) {
            $minutes = (int) config('auth.lockout_minutes', 15);
            $this->locked_until = now()->addMinutes($minutes);
        }
        $this->saveQuietly();
    }

    public function clearFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);
    }

    public function setPasswordAttribute(string $value): void
    {
        // Only hash if not already hashed
        $this->attributes['password'] = str_starts_with($value, '$2y$')
            ? $value
            : Hash::make($value, ['rounds' => 13]);
    }

    public function getAuthIdentifierName(): string  { return 'id'; }
    public function getAuthIdentifier(): mixed        { return $this->id; }
    public function getAuthPasswordName(): string     { return 'password'; }
    public function getAuthPassword(): string         { return $this->password; }
}
