<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = AdminUser::find(session('auth_user_id'));

        if (!$user) {
            return redirect()->route('auth.login');
        }

        return $user->isAdmin()
            ? $this->adminDashboard($user)
            : $this->stafDashboard($user);
    }

    // ── Admin Dashboard ────────────────────────────────────
    private function adminDashboard(AdminUser $user)
    {
        $stats = [
            'kabar' => [
                'published' => DB::table('konten')->where('label', 'KABAR')->where('status', 'published')->count(),
                'draft'     => DB::table('konten')->where('label', 'KABAR')->where('status', 'draft')->count(),
                'total'     => DB::table('konten')->where('label', 'KABAR')->count(),
            ],
            'komik' => [
                'published' => DB::table('konten')->where('label', 'KOMIK')->where('status', 'published')->count(),
                'draft'     => DB::table('konten')->where('label', 'KOMIK')->where('status', 'draft')->count(),
                'total'     => DB::table('konten')->where('label', 'KOMIK')->count(),
            ],
            'podcast' => [
                'published' => DB::table('konten')->where('label', 'PODCAST')->where('status', 'published')->count(),
                'draft'     => DB::table('konten')->where('label', 'PODCAST')->where('status', 'draft')->count(),
                'total'     => DB::table('konten')->where('label', 'PODCAST')->count(),
            ],
            'staf'     => DB::table('admin_users')->where('role', 'staf')->count(),
        ];

        $recentAudit = DB::table('audit_logs as a')
            ->leftJoin('admin_users as u', 'u.id', '=', 'a.admin_user_id')
            ->select('a.action', 'a.entity_type', 'a.entity_id', 'a.ip_address', 'a.created_at', 'u.username')
            ->orderByDesc('a.id')
            ->limit(8)
            ->get();

        return view('admin.dashboard.admin', compact('user', 'stats', 'recentAudit'));
    }

    // ── Staf Dashboard ─────────────────────────────────────
    private function stafDashboard(AdminUser $user)
    {
        $uid = $user->id;

        $stats = [
            'kabar' => [
                'published' => DB::table('konten')->where('label', 'KABAR')->where('status', 'published')->where('created_by', $uid)->count(),
                'draft'     => DB::table('konten')->where('label', 'KABAR')->where('status', 'draft')->where('created_by', $uid)->count(),
                'total'     => DB::table('konten')->where('label', 'KABAR')->where('created_by', $uid)->count(),
            ],
            'komik' => [
                'published' => DB::table('konten')->where('label', 'KOMIK')->where('status', 'published')->where('created_by', $uid)->count(),
                'draft'     => DB::table('konten')->where('label', 'KOMIK')->where('status', 'draft')->where('created_by', $uid)->count(),
                'total'     => DB::table('konten')->where('label', 'KOMIK')->where('created_by', $uid)->count(),
            ],
            'podcast' => [
                'published' => DB::table('konten')->where('label', 'PODCAST')->where('status', 'published')->where('created_by', $uid)->count(),
                'draft'     => DB::table('konten')->where('label', 'PODCAST')->where('status', 'draft')->where('created_by', $uid)->count(),
                'total'     => DB::table('konten')->where('label', 'PODCAST')->where('created_by', $uid)->count(),
            ],
        ];

        $recentAudit = DB::table('audit_logs')
            ->where('admin_user_id', $uid)
            ->select('action', 'entity_type', 'entity_id', 'ip_address', 'created_at')
            ->orderByDesc('id')
            ->limit(8)
            ->get();

        return view('admin.dashboard.staf', compact('user', 'stats', 'recentAudit'));
    }
}
