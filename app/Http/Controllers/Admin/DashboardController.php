<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'kabar'    => DB::table('kabar')->whereNull('deleted_at')->count(),
            'layanan'  => DB::table('layanan')->count(),
            'workshop' => DB::table('workshop')->whereNull('deleted_at')->count(),
            'pesan'    => DB::table('pesan_masuk')->where('is_read', false)->count(),
            'podcast'  => DB::table('podcast')->whereNull('deleted_at')->count(),
            'komik'    => DB::table('komik')->whereNull('deleted_at')->count(),
        ];

        $recentAudit = AuditLog::with('adminUser')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentAudit'));
    }
}
