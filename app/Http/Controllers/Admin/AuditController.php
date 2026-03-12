<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        $logs = DB::table('audit_logs as a')
            ->leftJoin('admin_users as u', 'u.id', '=', 'a.admin_user_id')
            ->select(
                'a.id',
                'a.action',
                'a.entity_type',
                'a.entity_id',
                'a.ip_address',
                'a.created_at',
                'u.username'
            )
            ->orderByDesc('a.id')
            ->paginate(50);

        return view('admin.audit.index', compact('logs'));
    }
}
