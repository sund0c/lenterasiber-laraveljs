<?php
// app/Http/Controllers/Admin/KomikController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomikController extends Controller
{
    public function index() {
        $items = DB::table('komik')->whereNull('deleted_at')->orderBy('sort_order')->paginate(20);
        return view('admin.komik.index', compact('items'));
    }
    public function create() { return view('admin.komik.form', ['item'=>null]); }
    public function store(Request $request) {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string'],
            'is_published' => ['boolean'],
            'sort_order'   => ['integer','min:0'],
        ]);
        $data['created_by'] = session('auth_user_id');
        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'komik');
        }
        $id = DB::table('komik')->insertGetId(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
        AuditLog::record('komik.create','komik',$id);
        return redirect()->route('admin.komik.index')->with('success','Komik disimpan.');
    }
    public function edit(int $id) {
        $item = DB::table('komik')->whereNull('deleted_at')->findOrFail($id);
        return view('admin.komik.form', compact('item'));
    }
    public function update(Request $request, int $id) {
        $data = $request->validate([
            'title'        => ['required','string','max:255'],
            'description'  => ['nullable','string'],
            'is_published' => ['boolean'],
            'sort_order'   => ['integer','min:0'],
        ]);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->storeImage($request->file('cover_image'), 'komik');
        }
        DB::table('komik')->where('id',$id)->update(array_merge($data,['updated_at'=>now()]));
        AuditLog::record('komik.update','komik',$id);
        return redirect()->route('admin.komik.index')->with('success','Komik diperbarui.');
    }
    public function destroy(int $id) {
        DB::table('komik')->where('id',$id)->update(['deleted_at'=>now()]);
        AuditLog::record('komik.delete','komik',$id);
        return redirect()->route('admin.komik.index')->with('success','Komik dihapus.');
    }
    private function storeImage($file, string $folder): string {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());
        if (!in_array($mime, ['image/jpeg','image/png','image/webp'])) abort(422, 'Tipe file tidak valid.');
        $ext  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime];
        $name = Str::random(32).'.'.$ext;
        $file->storeAs("uploads/{$folder}", $name, 'public');
        return "uploads/{$folder}/{$name}";
    }
}
