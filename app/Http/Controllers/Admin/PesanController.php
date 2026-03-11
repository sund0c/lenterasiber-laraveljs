<?php
// app/Http/Controllers/Admin/PesanController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class PesanController extends Controller
{
    public function index()
    {
        $items = DB::table('pesan_masuk')->orderByDesc('created_at')->paginate(20);
        return view('admin.pesan.index', compact('items'));
    }
    public function show(int $id)
    {
        $item = DB::table('pesan_masuk')->findOrFail($id);
        if (!$item->is_read) {
            DB::table('pesan_masuk')->where('id', $id)->update([
                'is_read' => true, 'read_at' => now(), 'read_by' => session('auth_user_id')
            ]);
        }
        return view('admin.pesan.show', compact('item'));
    }
    public function destroy(int $id)
    {
        DB::table('pesan_masuk')->where('id', $id)->delete();
        AuditLog::record('pesan.delete', 'pesan_masuk', $id);
        return redirect()->route('admin.pesan.index')->with('success', 'Pesan dihapus.');
    }
}
