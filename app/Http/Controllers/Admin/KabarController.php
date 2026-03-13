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

    // ── Guard: staf tidak bisa aksi pada artikel published ──
    private function guardPublished(object $item): void
    {
        if ($this->isStaf() && $item->status === 'published') {
            abort(403, 'Artikel yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    // ── Index ─────────────────────────────────────────────
    public function index()
    {
        $items = DB::table('kabar')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->get();
        return view('admin.kabar.index', compact('items'));
    }

    // ── Create ────────────────────────────────────────────
    public function create()
    {
        return view('admin.kabar.form', ['item' => null]);
    }

    // ── Store ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $this->validated($request);

        // Staf selalu draft
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

    // ── Show (view-only untuk staf pada artikel published) ─
    public function show(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        // Admin langsung ke edit
        if ($this->isAdmin()) {
            return redirect()->route('admin.kabar.edit', $id);
        }

        abort_if($item->status !== 'published', 403);
        return view('admin.kabar.show', compact('item'));
    }

    // ── Edit ──────────────────────────────────────────────
    public function edit(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);
        return view('admin.kabar.form', compact('item'));
    }

    // ── Update ────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

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

    // ── Destroy ───────────────────────────────────────────
    public function destroy(int $id)
    {
        $item = DB::table('kabar')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        DB::table('kabar')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('kabar.delete', 'kabar', $id);
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dihapus.');
    }

    // ── Publish / Unpublish (admin only) ──────────────────
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
        return redirect()->route('admin.kabar.index')->with('success', 'Artikel dipublikasikan.');
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

    // ── Validated ─────────────────────────────────────────
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'category'     => ['required', 'string', 'max:80'],
            'excerpt'      => ['required', 'string', 'max:100'],
            'content'      => ['required', 'string'],
            'read_minutes' => ['required', 'integer', 'min:1', 'max:60'],
            'status'       => ['nullable', 'in:draft,published'],
        ]);

        // Sanitize
        $data['title']    = strip_tags($data['title']);
        $data['category'] = strip_tags($data['category']);
        $data['excerpt']  = strip_tags($data['excerpt']);

        return $data;
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

    private function handleUpload(Request $request): string
    {
        $file  = $request->file('thumbnail');
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!array_key_exists($mime, $allowed)) {
            abort(422, 'Tipe file tidak valid. Hanya JPG, PNG, WebP.');
        }
        if ($file->getSize() > 2048 * 1024) {
            abort(422, 'Ukuran file melebihi 2MB.');
        }

        $name = Str::random(40) . '.' . $allowed[$mime];
        $file->storeAs('uploads/kabar', $name, 'public');
        return 'uploads/kabar/' . $name;
    }
}
