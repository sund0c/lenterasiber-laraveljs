<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUser extends Model
{

    protected $fillable = [
        'username',
        'full_name',
        'email',
        'password',
        'role',
        'totp_secret',
        'totp_enabled',
        'backup_codes',
        'failed_attempts',
        'locked_until',
        'last_login_at',
        'last_login_ip',
        'force_password_change',
    ];

    protected $hidden = ['password', 'totp_secret', 'backup_codes'];

    protected $casts = [
        'totp_enabled'          => 'boolean',
        'force_password_change' => 'boolean',
        'backup_codes'          => 'array',
        'locked_until'          => 'datetime',
        'last_login_at'         => 'datetime',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaf(): bool
    {
        return $this->role === 'staf';
    }

    public function isLocked(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }
    public function incrementFailedAttempts(): void
    {
        $this->increment('failed_attempts');

        // Kunci akun setelah 5 kali gagal
        if ($this->failed_attempts >= config('auth.max_attempts', 5)) {
            $this->update([
                'locked_until' => now()->addMinutes(config('auth.lockout_minutes', 15)),
            ]);
        }
    }

    public function clearFailedAttempts(): void
    {
        $this->update([
            'failed_attempts' => 0,
            'locked_until'    => null,
        ]);
    }
}
