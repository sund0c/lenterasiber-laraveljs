<?php
// app/Http/Controllers/Admin/LayananController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LayananController extends Controller
{
    public function index() {
        $items = DB::table('layanan')->orderBy('sort_order')->paginate(20);
        return view('admin.layanan.index', compact('items'));
    }
    public function create() { return view('admin.layanan.form', ['item'=>null]); }
    public function store(Request $request) {
        $data = $request->validate([
            'icon'       => ['nullable','string','max:80'],
            'title'      => ['required','string','max:150'],
            'short_desc' => ['nullable','string','max:300'],
            'full_content'=>['nullable','string'],
            'is_active'  => ['boolean'],
            'sort_order' => ['integer','min:0','max:99'],
        ]);
        $data['created_by'] = session('auth_user_id');
        $id = DB::table('layanan')->insertGetId(array_merge($data, ['created_at'=>now(),'updated_at'=>now()]));
        AuditLog::record('layanan.create','layanan',$id);
        return redirect()->route('admin.layanan.index')->with('success','Layanan disimpan.');
    }
    public function edit(int $id) {
        $item = DB::table('layanan')->findOrFail($id);
        return view('admin.layanan.form', compact('item'));
    }
    public function update(Request $request, int $id) {
        $data = $request->validate([
            'icon'       => ['nullable','string','max:80'],
            'title'      => ['required','string','max:150'],
            'short_desc' => ['nullable','string','max:300'],
            'full_content'=>['nullable','string'],
            'is_active'  => ['boolean'],
            'sort_order' => ['integer','min:0','max:99'],
        ]);
        DB::table('layanan')->where('id',$id)->update(array_merge($data,['updated_at'=>now()]));
        AuditLog::record('layanan.update','layanan',$id);
        return redirect()->route('admin.layanan.index')->with('success','Layanan diperbarui.');
    }
    public function destroy(int $id) {
        DB::table('layanan')->where('id',$id)->delete();
        AuditLog::record('layanan.delete','layanan',$id);
        return redirect()->route('admin.layanan.index')->with('success','Layanan dihapus.');
    }
}
