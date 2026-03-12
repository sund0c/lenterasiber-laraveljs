<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdminUser;

trait ChecksRole
{
    /**
     * Ambil user yang sedang login dari session.
     */
    protected function currentUser(): ?AdminUser
    {
        return AdminUser::find(session('auth_user_id'));
    }

    /**
     * Return true jika user adalah admin.
     */
    protected function isAdmin(): bool
    {
        return $this->currentUser()?->isAdmin() ?? false;
    }

    /**
     * Return true jika user adalah staf.
     */
    protected function isStaf(): bool
    {
        return $this->currentUser()?->isStaf() ?? false;
    }

    /**
     * Abort 403 jika bukan admin.
     */
    protected function requireAdmin(): void
    {
        if (!$this->isAdmin()) {
            abort(403, 'Akses ditolak. Hanya admin yang dapat melakukan aksi ini.');
        }
    }
}
