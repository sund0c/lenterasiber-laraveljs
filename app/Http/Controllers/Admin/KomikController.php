<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomikController extends Controller
{
    // ── Helper ────────────────────────────────────────────────
    private function currentUser(): object
    {
        $user = DB::table('admin_users')
            ->where('id', session('auth_user_id'))
            ->first();

        abort_if(!$user, 403);
        return $user;
    }

    private function isAdmin(): bool
    {
        return $this->currentUser()->role === 'admin';
    }

    private function guardPublished(object $item): void
    {
        if (!$this->isAdmin() && $item->is_published) {
            abort(403, 'Komik yang sudah dipublikasikan hanya bisa diubah oleh Admin.');
        }
    }

    // ── Index ─────────────────────────────────────────────────
    public function index()
    {
        $items = DB::table('komik')
            ->whereNull('deleted_at')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('admin.komik.index', compact('items'));
    }

    // ── Create ────────────────────────────────────────────────
    public function create()
    {
        return view('admin.komik.form', ['item' => null]);
    }

    // ── Store ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            $data['is_published'] = false;
        }

        $data['created_by']  = session('auth_user_id');
        $data['cover_image'] = $this->handleUpload($request);

        $id = DB::table('komik')->insertGetId(array_merge($data, [
            'created_at' => now(),
            'updated_at' => now(),
        ]));

        AuditLog::record('komik.create', 'komik', $id, null, ['title' => $data['title']]);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil disimpan.');
    }

    // ── Edit ──────────────────────────────────────────────────
    public function edit(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        return view('admin.komik.form', compact('item'));
    }

    // ── Update ────────────────────────────────────────────────
    public function update(Request $request, int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        $data = $this->validated($request);

        if (!$this->isAdmin()) {
            unset($data['is_published']);
        }

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->handleUpload($request);
        }

        DB::table('komik')->where('id', $id)->update(array_merge($data, [
            'updated_at' => now(),
        ]));

        AuditLog::record('komik.update', 'komik', $id, (array) $item, $data);
        return redirect()->route('admin.komik.index')->with('success', 'Komik berhasil diperbarui.');
    }

    // ── Destroy ───────────────────────────────────────────────
    public function destroy(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);
        $this->guardPublished($item);

        DB::table('komik')->where('id', $id)->update(['deleted_at' => now()]);
        AuditLog::record('komik.delete', 'komik', $id);
        return redirect()->route('admin.komik.index')->with('success', 'Komik dihapus.');
    }

    // ── Validated ─────────────────────────────────────────────
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title'          => ['required', 'string', 'max:255'],
            'episode_number' => ['required', 'string', 'max:20'],
            'category'       => ['required', 'string', 'max:100'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'instagram_url'  => ['required', 'url', 'max:500'],
            'sort_order'     => ['required', 'integer', 'min:0'],
            'is_published'   => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        // Sanitize string fields — strip tags
        foreach (['title', 'episode_number', 'category', 'description'] as $field) {
            $data[$field] = strip_tags($data[$field]);
        }

        return $data;
    }

    // ── Handle Upload ─────────────────────────────────────────
    private function handleUpload(Request $request): ?string
    {
        if (!$request->hasFile('cover_image')) return null;

        $file = $request->file('cover_image');

        // Validasi MIME via finfo (bukan dari ekstensi/header HTTP)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file->getRealPath());

        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

        if (!array_key_exists($mime, $allowed)) {
            abort(422, 'Tipe file tidak valid. Hanya JPG, PNG, WebP.');
        }
        if ($file->getSize() > 2048 * 1024) {
            abort(422, 'Ukuran file melebihi 2MB.');
        }

        // Nama file random — tidak pakai nama asli dari user
        $name = Str::random(40) . '.' . $allowed[$mime];
        $file->storeAs('uploads/komik', $name, 'public');

        return 'uploads/komik/' . $name;
    }
    // ── Show (view-only untuk staf pada komik published) ──────
    public function show(int $id)
    {
        $item = DB::table('komik')->whereNull('deleted_at')->where('id', $id)->first();
        abort_if(!$item, 404);

        // Admin tidak perlu halaman view-only — redirect ke edit
        if ($this->isAdmin()) {
            return redirect()->route('admin.komik.edit', $id);
        }

        // Staf hanya bisa lihat yang published
        abort_if(!$item->is_published, 403);

        return view('admin.komik.show', compact('item'));
    }
}
