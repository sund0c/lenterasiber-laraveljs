<?php
// app/Http/Controllers/Admin/AuditController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class AuditController extends Controller
{
    public function index()
    {
        $logs = AuditLog::with('adminUser')
            ->orderByDesc('created_at')
            ->paginate(30);
        return view('admin.audit.index', compact('logs'));
    }
}
