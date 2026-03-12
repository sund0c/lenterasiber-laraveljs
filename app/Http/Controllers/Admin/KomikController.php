<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomikController extends Controller
{
    public function index()
    {
        $items = DB::table('komik')->whereNull('deleted_at')
            ->orderBy('sort_order')->orderBy('id')->get();
        return view('admin.komik.index', compact('items'));
    }

    public function create()
    {
        return view('admin.komik.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by']  = session('auth_user_id');
        $data['cover_image'] = $this->handleUpload($request);

        $id = DB::table('komik')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('komik.create', 'komik', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        return view('admin.komik.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        $data = $this->validated($request);
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->handleUpload($request);
        }

        DB::table('komik')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        AuditLog::record('komik.update', 'komik', $id, (array) $item, $data);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('komik')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('komik.delete', 'komik', $id);
        return redirect()->route('admin.komik.index')->with('success', 'Komik dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'           => ['required', 'string', 'max:255'],
            'episode_number'  => ['nullable', 'string', 'max:20'],
            'category'        => ['nullable', 'string', 'max:100'],
            'description'     => ['nullable', 'string'],
            'instagram_url'   => ['nullable', 'url', 'max:500'],
            'is_published'    => ['nullable', 'boolean'],
            'sort_order'      => ['nullable', 'integer', 'min:0'],
        ]) + ['is_published' => $request->boolean('is_published'), 'sort_order' => $request->input('sort_order', 0)];
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('cover_image')) return null;
        $file  = $request->file('cover_image');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) abort(422, 'Tipe file tidak valid.');
        if ($file->getSize() > 2048 * 1024) abort(422, 'File terlalu besar.');
        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/komik', $name, 'public');
        return 'uploads/komik/' . $name;
    }
}
