<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminUser;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = AdminUser::find(session('auth_user_id'));

        if (!$user) {
            return redirect()->route('auth.login');
        }

        if ($user->isAdmin()) {
            return $this->adminDashboard($user);
        }

        return $this->stafDashboard($user);
    }

    // ── Admin Dashboard ────────────────────────────────────
    private function adminDashboard(AdminUser $user)
    {
        $stats = [
            'kabar' => [
                'published' => DB::table('kabar')->whereNull('deleted_at')->where('status', 'published')->count(),
                'draft'     => DB::table('kabar')->whereNull('deleted_at')->where('status', 'draft')->count(),
                'total'     => DB::table('kabar')->whereNull('deleted_at')->count(),
            ],
            'podcast' => [
                'published' => DB::table('podcast')->whereNull('deleted_at')->where('is_published', true)->count(),
                'draft'     => DB::table('podcast')->whereNull('deleted_at')->where('is_published', false)->count(),
                'total'     => DB::table('podcast')->whereNull('deleted_at')->count(),
            ],
            'komik' => [
                'published' => DB::table('komik')->whereNull('deleted_at')->where('is_published', true)->count(),
                'draft'     => DB::table('komik')->whereNull('deleted_at')->where('is_published', false)->count(),
                'total'     => DB::table('komik')->whereNull('deleted_at')->count(),
            ],
            'layanan'  => DB::table('layanan')->count(),
            'workshop' => DB::table('workshop')->whereNull('deleted_at')->count(),
            'staf' => DB::table('admin_users')->where('role', 'staf')->count(),
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
                'published' => DB::table('kabar')->whereNull('deleted_at')->where('status', 'published')->where('created_by', $uid)->count(),
                'draft'     => DB::table('kabar')->whereNull('deleted_at')->where('status', 'draft')->where('created_by', $uid)->count(),
                'total'     => DB::table('kabar')->whereNull('deleted_at')->where('created_by', $uid)->count(),
            ],
            'podcast' => [
                'published' => DB::table('podcast')->whereNull('deleted_at')->where('is_published', true)->where('created_by', $uid)->count(),
                'draft'     => DB::table('podcast')->whereNull('deleted_at')->where('is_published', false)->where('created_by', $uid)->count(),
                'total'     => DB::table('podcast')->whereNull('deleted_at')->where('created_by', $uid)->count(),
            ],
            'komik' => [
                'published' => DB::table('komik')->whereNull('deleted_at')->where('is_published', true)->where('created_by', $uid)->count(),
                'draft'     => DB::table('komik')->whereNull('deleted_at')->where('is_published', false)->where('created_by', $uid)->count(),
                'total'     => DB::table('komik')->whereNull('deleted_at')->where('created_by', $uid)->count(),
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
