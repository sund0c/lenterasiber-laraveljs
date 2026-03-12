<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KabarController extends Controller
{
    public function index()
    {
        $items = DB::table('kabar')->whereNull('deleted_at')
            ->orderBy('sort_order')->orderByDesc('id')->get();
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
        $data['slug']       = $this->uniqueSlug($data['slug'] ?: $data['title']);

        $id = DB::table('kabar')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('kabar.create', 'kabar', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.kabar.index')->with('success', 'Kabar berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        return view('admin.kabar.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        $data = $this->validated($request, $id);
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }
        $data['slug'] = empty($data['slug']) ? $item->slug : $this->uniqueSlug($data['slug'], $id);

        DB::table('kabar')->where('id', $id)->update(array_merge($data, ['updated_at' => now()]));

        AuditLog::record('kabar.update', 'kabar', $id, (array) $item, $data);
        return redirect()->route('admin.kabar.index')->with('success', 'Kabar berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        DB::table('kabar')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('kabar.delete', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Kabar dihapus.');
    }

    private function validated(Request $request, ?int $excludeId = null): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'slug'         => ['nullable', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:80'],
            'excerpt'      => ['nullable', 'string', 'max:500'],
            'content'      => ['nullable', 'string'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'status'       => ['nullable', 'in:draft,published'],
        ]);

        $data['status']       = $request->input('status', 'draft');
        $data['read_minutes'] = $data['read_minutes'] ?? 3;
        return $data;
    }

    private function uniqueSlug(string $base, ?int $excludeId = null): string
    {
        $slug     = Str::slug($base);
        $original = $slug;
        $i        = 1;
        while (true) {
            $query = DB::table('kabar')->whereNull('deleted_at')->where('slug', $slug);
            if ($excludeId) $query->where('id', '!=', $excludeId);
            if (!$query->exists()) break;
            $slug = $original . '-' . $i++;
        }
        return $slug;
    }

    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('thumbnail')) return null;
        $file  = $request->file('thumbnail');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) abort(422, 'Tipe file tidak valid.');
        if ($file->getSize() > 2048 * 1024) abort(422, 'File terlalu besar.');
        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/kabar', $name, 'public');
        return 'uploads/kabar/' . $name;
    }
}
