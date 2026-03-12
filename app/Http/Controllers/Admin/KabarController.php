<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KabarController extends Controller
{
    use ChecksRole;

    public function index()
    {
        $items = DB::table('kabar')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();
        return view('admin.kabar.index', compact('items'));
    }

    public function create()
    {
        return view('admin.kabar.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Staf hanya boleh draft
        if ($this->isStaf()) {
            $data['status'] = 'draft';
        }

        $data['created_by'] = session('auth_user_id');
        $data['slug']       = $this->makeSlug($data['title']);

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        $id = DB::table('kabar')->insertGetId(array_merge($data, [
            'view_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('kabar.create', 'kabar', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        // Staf hanya bisa edit data draft milik sendiri
        if ($this->isStaf()) {
            if ($item->status !== 'draft' || $item->created_by !== session('auth_user_id')) {
                abort(403, 'Staf hanya dapat mengedit artikel draft milik sendiri.');
            }
        }

        return view('admin.kabar.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        // Staf hanya bisa edit data draft milik sendiri
        if ($this->isStaf()) {
            if ($item->status !== 'draft' || $item->created_by !== session('auth_user_id')) {
                abort(403, 'Staf hanya dapat mengedit artikel draft milik sendiri.');
            }
        }

        $data = $this->validated($request);

        // Staf tidak bisa publish
        if ($this->isStaf()) {
            $data['status'] = 'draft';
        }

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $this->handleUpload($request);
        }

        DB::table('kabar')->where('id', $id)->update(array_merge($data, [
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]));

        AuditLog::record('kabar.update', 'kabar', $id, (array) $item, $data);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        // Staf hanya bisa hapus draft milik sendiri
        if ($this->isStaf()) {
            if ($item->status !== 'draft' || $item->created_by !== session('auth_user_id')) {
                abort(403, 'Staf hanya dapat menghapus artikel draft milik sendiri.');
            }
        }

        DB::table('kabar')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('kabar.delete', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dihapus.');
    }

    // ── Publish (admin only) ───────────────────────────────
    public function publish(int $id)
    {
        $this->requireAdmin();

        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        DB::table('kabar')->where('id', $id)->update([
            'status'       => 'published',
            'published_at' => now(),
            'updated_by'   => session('auth_user_id'),
            'updated_at'   => now(),
        ]);

        AuditLog::record('kabar.publish', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dipublish.');
    }

    public function unpublish(int $id)
    {
        $this->requireAdmin();

        DB::table('kabar')->where('id', $id)->update([
            'status'     => 'draft',
            'updated_by' => session('auth_user_id'),
            'updated_at' => now(),
        ]);

        AuditLog::record('kabar.unpublish', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dikembalikan ke draft.');
    }

    // ── Private helpers ────────────────────────────────────
    private function validated(Request $request): array
    {
        return $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['nullable', 'string', 'max:80'],
            'excerpt'      => ['nullable', 'string', 'max:500'],
            'content'      => ['nullable', 'string'],
            'read_minutes' => ['nullable', 'integer', 'min:1', 'max:60'],
            'status'       => ['nullable', 'in:draft,published'],
        ]);
    }

    private function makeSlug(string $title): string
    {
        $slug = Str::slug($title);
        $orig = $slug;
        $i    = 1;
        while (DB::table('kabar')->where('slug', $slug)->exists()) {
            $slug = $orig . '-' . $i++;
        }
        return $slug;
    }

    private function handleUpload(Request $request): ?string
    {
        $file  = $request->file('thumbnail');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) abort(422, 'Tipe file tidak valid.');
        if ($file->getSize() > 2048 * 1024) abort(422, 'File terlalu besar. Maks 2MB.');
        $ext  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime];
        $name = Str::random(32) . '.' . $ext;
        $file->storeAs('uploads/kabar', $name, 'public');
        return 'uploads/kabar/' . $name;
    }
}
