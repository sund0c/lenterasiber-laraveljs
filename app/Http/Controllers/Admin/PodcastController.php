<?php
// app/Http/Controllers/Admin/PodcastController.php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PodcastController extends Controller
{
    public function index() {
        $items = DB::table('podcast')->whereNull('deleted_at')->orderByDesc('published_date')->paginate(20);
        return view('admin.podcast.index', compact('items'));
    }
    public function create() { return view('admin.podcast.form', ['item'=>null]); }
    public function store(Request $request) {
        $data = $request->validate([
            'title'            => ['required','string','max:255'],
            'description'      => ['nullable','string'],
            'episode_number'   => ['nullable','string','max:20'],
            'audio_url'        => ['nullable','url','max:500'],
            'duration_minutes' => ['nullable','integer','min:1'],
            'is_published'     => ['boolean'],
            'published_date'   => ['nullable','date'],
        ]);
        $data['created_by'] = session('auth_user_id');
        $id = DB::table('podcast')->insertGetId(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
        AuditLog::record('podcast.create','podcast',$id);
        return redirect()->route('admin.podcast.index')->with('success','Episode disimpan.');
    }
    public function edit(int $id) {
        $item = DB::table('podcast')->whereNull('deleted_at')->findOrFail($id);
        return view('admin.podcast.form', compact('item'));
    }
    public function update(Request $request, int $id) {
        $data = $request->validate([
            'title'            => ['required','string','max:255'],
            'description'      => ['nullable','string'],
            'episode_number'   => ['nullable','string','max:20'],
            'audio_url'        => ['nullable','url','max:500'],
            'duration_minutes' => ['nullable','integer','min:1'],
            'is_published'     => ['boolean'],
            'published_date'   => ['nullable','date'],
        ]);
        DB::table('podcast')->where('id',$id)->update(array_merge($data,['updated_at'=>now()]));
        AuditLog::record('podcast.update','podcast',$id);
        return redirect()->route('admin.podcast.index')->with('success','Episode diperbarui.');
    }
    public function destroy(int $id) {
        DB::table('podcast')->where('id',$id)->update(['deleted_at'=>now()]);
        AuditLog::record('podcast.delete','podcast',$id);
        return redirect()->route('admin.podcast.index')->with('success','Episode dihapus.');
    }
}
