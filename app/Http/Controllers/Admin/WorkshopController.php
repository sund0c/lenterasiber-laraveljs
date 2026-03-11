<?php
// app/Http/Controllers/Admin/WorkshopController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WorkshopController extends Controller
{
    public function index() {
        $items = DB::table('workshop')->whereNull('deleted_at')->orderByDesc('event_date')->paginate(20);
        return view('admin.workshop.index', compact('items'));
    }
    public function create() { return view('admin.workshop.form', ['item'=>null]); }
    public function store(Request $request) {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'event_date'  => ['nullable','date'],
            'location'    => ['nullable','string','max:255'],
            'capacity'    => ['nullable','integer','min:1'],
            'status'      => ['required', Rule::in(['upcoming','ongoing','completed','cancelled'])],
        ]);
        $data['created_by'] = session('auth_user_id');
        $id = DB::table('workshop')->insertGetId(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
        AuditLog::record('workshop.create','workshop',$id);
        return redirect()->route('admin.workshop.index')->with('success','Workshop disimpan.');
    }
    public function edit(int $id) {
        $item = DB::table('workshop')->whereNull('deleted_at')->findOrFail($id);
        return view('admin.workshop.form', compact('item'));
    }
    public function update(Request $request, int $id) {
        $data = $request->validate([
            'title'       => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'event_date'  => ['nullable','date'],
            'location'    => ['nullable','string','max:255'],
            'capacity'    => ['nullable','integer','min:1'],
            'status'      => ['required', Rule::in(['upcoming','ongoing','completed','cancelled'])],
        ]);
        DB::table('workshop')->where('id',$id)->update(array_merge($data,['updated_at'=>now()]));
        AuditLog::record('workshop.update','workshop',$id);
        return redirect()->route('admin.workshop.index')->with('success','Workshop diperbarui.');
    }
    public function destroy(int $id) {
        DB::table('workshop')->where('id',$id)->update(['deleted_at'=>now()]);
        AuditLog::record('workshop.delete','workshop',$id);
        return redirect()->route('admin.workshop.index')->with('success','Workshop dihapus.');
    }
}
