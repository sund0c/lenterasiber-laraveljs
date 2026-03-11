<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KabarController extends Controller
{
    private const ALLOWED_MIME = ['image/jpeg','image/png','image/webp'];
    private const MAX_SIZE_KB  = 2048;

    public function index(Request $request)
    {
        $query = DB::table('kabar')->whereNull('deleted_at');

        if ($q = $request->get('q')) {
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', '%' . $q . '%')
                  ->orWhere('excerpt', 'like', '%' . $q . '%');
            });
        }
        if ($cat = $request->get('cat')) {
            $query->where('category', $cat);
        }

        $items = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        return view('admin.kabar.index', compact('items'));
    }

    public function create()
    {
        return view('admin.kabar.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = session('auth_user_id');
        $data['thumbnail']  = $this->handleUpload($request);

        $id = DB::table('kabar')->insertGetId(array_merge($data, [
            'created_at' => now(), 'updated_at' => now(),
        ]));

        AuditLog::record('kabar.create', 'kabar', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->findOrFail($id);
        return view('admin.kabar.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->findOrFail($id);
        $data = $this->validated($request, $id);
        $data['updated_by'] = session('auth_user_id');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        DB::table('kabar')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        AuditLog::record('kabar.update', 'kabar', $id, (array)$item, $data);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('kabar')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('kabar.delete', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dihapus.');
    }

    private function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'slug'         => ['required', 'string', 'max:160', 'alpha_dash',
                               Rule::unique('kabar')->whereNull('deleted_at')->ignore($id)],
            'excerpt'      => ['nullable', 'string', 'max:400'],
            'content'      => ['required', 'string'],
            'category'     => ['required', Rule::in(['edukasi','ancaman','tips','regulasi','event'])],
            'status'       => ['required', Rule::in(['draft','published'])],
            'published_at' => ['nullable', 'date'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:120'],
            'meta_title'   => ['nullable', 'string', 'max:70'],
            'meta_desc'    => ['nullable', 'string', 'max:160'],
        ]);
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('thumbnail')) return null;

        $file = $request->file('thumbnail');

        // Validate MIME via finfo (not just extension)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        if (!in_array($mime, self::ALLOWED_MIME)) {
            abort(422, 'Tipe file tidak diizinkan.');
        }
        if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
            abort(422, 'Ukuran file melebihi batas.');
        }

        // Store with random name (no user-controlled filename)
        $ext  = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/kabar', $name, 'public');

        return 'uploads/kabar/' . $name;
    }
}
